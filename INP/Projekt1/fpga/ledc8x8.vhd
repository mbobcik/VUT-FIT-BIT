library IEEE;
use IEEE.std_logic_1164.all;
use IEEE.std_logic_arith.all;
use IEEE.std_logic_unsigned.all;

entity ledc8x8 is
port ( -- Sem doplnte popis rozhrani obvodu.
	SMCLK: in std_logic;
	RESET: in std_logic;
	LED: out std_logic_vector(7 downto 0);
	ROW: out std_logic_vector(7 downto 0)
);
end ledc8x8;

architecture main of ledc8x8 is
	signal ce: std_logic := '0';
	signal switch: std_logic := '0';
	signal counter: std_logic_vector(22 downto 0);
	signal dec_out: std_logic_vector(7 downto 0) := "00000000";
	signal row_inner: std_logic_vector(7 downto 0) := "10000000";
	signal char_cnt: std_logic_vector(7 downto 0) := "00000000";
    -- Sem doplnte definice vnitrnich signalu.

begin

    -- Sem doplnte popis funkce obvodu (zakladni konstrukce VHDL jako napr.
    -- prirazeni signalu, multiplexory, dekodery, procesy...).
    -- DODRZUJTE ZASADY PSANI SYNTETIZOVATELNEHO VHDL UVEDENE NA WEBU:
    -- http://merlin.fit.vutbr.cz/FITkit/docs/navody/synth_templates.html

    -- Nezapomente take doplnit mapovani signalu rozhrani na piny FPGA
    -- v souboru ledc8x8.ucf.

	-- 1/256 SMCLK
	ctrl_cnt: process (SMCLK, RESET)
	begin
		if RESET = '1'
		then
			counter <= "00000000000000000000000";
		elsif rising_edge(SMCLK)
		then
			if counter(7 downto 0) = "11111111"
			then
				ce <= '1';
			else
				ce <= '0';
			end if;
      
			switch <= counter(22);
			counter <= counter + 1;
		end if;
	end process;

	-- rotacni registr
	row_cnt: process (SMCLK, RESET, row_inner)
	begin
		-- asynchroni reset
		if RESET = '1'
		then
			ROW <= "10000000";
			row_inner <= "10000000";
		elsif rising_edge(SMCLK) AND ce = '1'
		then
			case row_inner is
				when "10000000" => row_inner <= "01000000";
				when "00000001" => row_inner <= "10000000";
				when "00000010" => row_inner <= "00000001";
				when "00000100" => row_inner <= "00000010";
				when "00001000" => row_inner <= "00000100";
				when "00010000" => row_inner <= "00001000";
				when "00100000" => row_inner <= "00010000";
				when "01000000" => row_inner <= "00100000";
				when others => null;
			end case;
		end if;

		ROW <= row_inner;
	end process;

-- dekoder
	dec: process (SMCLK, dec_out)
	begin
		if rising_edge(SMCLK)
		then
			if switch = '0'--cosik
			then
				case row_inner is
					when "00000001" => dec_out <= "11110000";
					when "00000010" => dec_out <= "11101110";
					when "00000100" => dec_out <= "11101110";
					when "00001000" => dec_out <= "11110000";
					when "00010000" => dec_out <= "11101110";
					when "00100000" => dec_out <= "11101110";
					when "01000000" => dec_out <= "11101110";
					when "10000000" => dec_out <= "11110000";
					when others => null;
				end case;
			else
				case row_inner is
					when "00000001" => dec_out <= "01111110";
					when "00000010" => dec_out <= "00111100";
					when "00000100" => dec_out <= "01011010";
					when "00001000" => dec_out <= "01100110";
					when "00010000" => dec_out <= "01111110";
					when "00100000" => dec_out <= "01111110";
					when "01000000" => dec_out <= "01111110";
	  			when "10000000" => dec_out <= "01111110";
					when others => null;
				end case;
			end if;
		end if;
	end process;

	lighter : process( SMCLK, RESET, dec_out )
	begin
		if RESET = '1' then
			LED <="11111111";
		elsif rising_edge(SMCLK) then
				LED<=dec_out;
		end if ;
	end process ; -- lighter

end main;
