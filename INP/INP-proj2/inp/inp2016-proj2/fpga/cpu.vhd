-- cpu.vhd: Simple 8-bit CPU (BrainLove interpreter)
-- Copyright (C) 2016 Brno University of Technology,
--                    Faculty of Information Technology
-- Author(s): Martin Bobcik xbobci00
--

library ieee;
use ieee.std_logic_1164.all;
use ieee.std_logic_arith.all;
use ieee.std_logic_unsigned.all;

-- ----------------------------------------------------------------------------
--                        Entity declaration
-- ----------------------------------------------------------------------------
entity cpu is
 port (
   CLK   : in std_logic;  -- hodinovy signal
   RESET : in std_logic;  -- asynchronni reset procesoru
   EN    : in std_logic;  -- povoleni cinnosti procesoru
 
   -- synchronni pamet ROM
   CODE_ADDR : out std_logic_vector(11 downto 0); -- adresa do pameti
   CODE_DATA : in std_logic_vector(7 downto 0);   -- CODE_DATA <- rom[CODE_ADDR] pokud CODE_EN='1'
   CODE_EN   : out std_logic;                     -- povoleni cinnosti
   
   -- synchronni pamet RAM
   DATA_ADDR  : out std_logic_vector(9 downto 0); -- adresa do pameti
   DATA_WDATA : out std_logic_vector(7 downto 0); -- mem[DATA_ADDR] <- DATA_WDATA pokud DATA_EN='1'
   DATA_RDATA : in std_logic_vector(7 downto 0);  -- DATA_RDATA <- ram[DATA_ADDR] pokud DATA_EN='1'
   DATA_RDWR  : out std_logic;                    -- cteni (1) / zapis (0)
   DATA_EN    : out std_logic;                    -- povoleni cinnosti
   
   -- vstupni port
   IN_DATA   : in std_logic_vector(7 downto 0);   -- IN_DATA <- stav klavesnice pokud IN_VLD='1' a IN_REQ='1'
   IN_VLD    : in std_logic;                      -- data platna
   IN_REQ    : out std_logic;                     -- pozadavek na vstup data
   
   -- vystupni port
   OUT_DATA : out  std_logic_vector(7 downto 0);  -- zapisovana data
   OUT_BUSY : in std_logic;                       -- LCD je zaneprazdnen (1), nelze zapisovat
   OUT_WE   : out std_logic                       -- LCD <- OUT_DATA pokud OUT_WE='1' a OUT_BUSY='0'
 );
end cpu;


-- ----------------------------------------------------------------------------
--                      Architecture declaration
-- ----------------------------------------------------------------------------
architecture behavioral of cpu is
signal PC_addr: std_logic_vector(11 downto 0) := (others => '0');
signal PC_inc: std_logic := '0';
signal PC_dec: std_logic:= '0';

signal PTR_data: std_logic_vector(9 downto 0):= (others => '0');
signal PTR_inc: std_logic:= '0';
signal PTR_dec: std_logic:= '0';

signal TMP_data: std_logic_vector(7 downto 0):= (others => '0');
signal TMP_read: std_logic := '0';
signal TMP_write: std_logic := '0';

signal MXselect: std_logic_vector(2 downto 0):= (others => '0');

type state is (INIT,WHILESTART,
WHILEEND, DECODE, INCPTR, DECPTR,
INCDATA,INCDATA2, DECDATA, DECDATA2, READTMP,
WRITETMP, WAITEXE,
PRINTDATA, READDATA, NOOP, RET);
signal nextState: state;
signal presentState: state;

begin

--with MXselect select
--   DATA_WDATA <= IN_DATA when "000",
--   DATA_WDATA <= TMP_data when "001",
--   DATA_WDATA <= (DATA_RDATA - "00000001") when "010",
--   DATA_WDATA <= (DATA_RDATA + "00000001") when "011",
--   (others => '0') when others;

mulplex: process (CLK, MXselect, DATA_RDATA, IN_DATA)
begin
    case MXselect is
        when "000" => DATA_WDATA <= IN_DATA;
        when "001" => DATA_WDATA <= TMP_data;
        when "010" => DATA_WDATA <= (DATA_RDATA - "00000001");
        when "011" => DATA_WDATA <= (DATA_RDATA + "00000001");
        when others => 
    end case;
end process;

--fsm
fsmPstate : process( CLK, RESET )
begin
   if (RESET = '1') then
      presentState <= INIT;
   elsif (CLK'event and CLK = '1') then
      if (EN = '1') then
         presentState <= nextState;
      end if ;
   end if ;
end process ; -- fsmPstate
                                 
fsm_Nstate : process( presentState, CLK, RESET, EN, CODE_DATA, DATA_RDATA, IN_DATA, IN_VLD, OUT_BUSY )
begin
   nextState <= INIT;
   CODE_EN <='0';
   PTR_inc <= '0';
   PTR_dec <= '0';
   PC_inc <= '0';
   PC_dec <= '0';
   TMP_read <= '0';
   TMP_write <= '0';
   DATA_EN <= '0';
   OUT_WE <= '0';
   DATA_RDWR <= '1';
   IN_REQ <= '0';
   MXselect <= "111";
   
   case( presentState ) is
   
      when WAITEXE =>
         CODE_EN <='1';
         PC_inc <= '1';
         nextState <= DECODE;

      when INIT =>
         CODE_EN <='1';
         DATA_EN <= '1';
         nextState <= DECODE;
      when DECODE =>

         case( CODE_DATA ) is
            when X"3E" =>
               nextState <= INCPTR;
            when X"3C" =>
               nextState <= DECPTR;
            when X"2B" =>
               nextState <= INCDATA;
            when X"2D" =>
               nextState <= DECDATA;
            when X"5B" =>
               nextState <= WHILESTART;
            when X"5D" =>
               nextState <= WHILEEND;
            when X"2E" =>
               nextState <= PRINTDATA;
            when X"2C" =>
               nextState <= READDATA;
            when X"24" =>
               nextState <= WRITETMP;
            when X"21" =>
               nextState <= READTMP;
            when X"00" =>
               nextState <= RET;
            when others =>
               nextState <= NOOP;
         end case ;
      
      when INCPTR=>
         PTR_inc <= '1';
         nextState <= WAITEXE;
      when DECPTR=>
         PTR_dec <= '1';
         nextState <= WAITEXE;

      when INCDATA =>
         DATA_RDWR <= '1';
         DATA_EN <= '1';
         nextState <= INCDATA2;
      when INCDATA2=>
         MXselect <="011";
         DATA_RDWR <= '0';
         DATA_EN <= '1';
         nextState <=WAITEXE;

      when DECDATA =>
         DATA_RDWR <= '1';
         DATA_EN <= '1';
         nextState <= DECDATA2;
      when DECDATA2=>
         MXselect <="010";
         DATA_RDWR <= '0';
         DATA_EN <= '1';
         nextState <=WAITEXE;

      when READTMP =>
         DATA_EN <= '1';
         DATA_RDWR <= '0';

         --TMP_read <= '1';
         MXselect <= "001";

         nextState <= WAITEXE;
      when WRITETMP =>
         DATA_EN <= '1';
         DATA_RDWR <= '1';

         --TMP_write <= '1';
         TMP_data <= DATA_RDATA;

         nextState <= WAITEXE;

      when PRINTDATA =>
         if (OUT_BUSY = '0') then
         DATA_RDWR <= '1';
            DATA_EN <= '1';
            OUT_DATA <= DATA_RDATA;
            OUT_WE <= '1';
            nextState <= WAITEXE;
            
         else
            nextState <= PRINTDATA;
         end if ;

      when READDATA =>
         IN_REQ <= '1';
         if (IN_VLD <= '0') then
            nextState <= READDATA;
         else
            MXselect <= "000";
            DATA_RDWR <= '0';
            DATA_EN <= '1';
            nextState <= WAITEXE;
         end if ;
      when NOOP =>
         nextState <= WAITEXE;
      when RET => null;

      when others => null;
   
   end case ;
end process ; -- fsm_Nstate

--PC
 prog_cntr: process (RESET, CLK)
 begin
   if (RESET = '1') then
      CODE_ADDR <= (others => '0');
   elsif (CLK'event) and (CLK ='1') then
         if (PC_inc = '1') then
            PC_addr <= PC_addr + 1;
         elsif (PC_dec = '1') then
            PC_addr <= PC_addr - 1;
         end if ;
   end if;
   CODE_ADDR <= PC_addr;
 end process;

--ptr 
ptr : process( CLK,RESET )
begin
   if (RESET ='1') then
      PTR_data <= (others => '0');
   elsif (CLK'event and CLK ='1') then
      if (PTR_inc = '1') then
         PTR_data <= PTR_data +1;
      elsif (PTR_dec = '1') then
         PTR_data <= PTR_data - 1;
      end if ;
   end if ;
   DATA_ADDR <= PTR_data;
end process ; -- ptr

----tmp
--tmp : process( CLK, RESET )
--begin
--   if (RESET = '1') then
--      TMP_data <= (others => '0');
--   elsif (CLK'event and CLK ='1') then
--      if (TMP_read = '1') then
--         --DATA_WDATA <= TMP_data;
--         MXselect <= "001";
--      elsif (TMP_write = '1') then
--         TMP_data <= DATA_RDATA;
--      end if ;
--   end if ;
--end process ; -- tmp

end behavioral;
 