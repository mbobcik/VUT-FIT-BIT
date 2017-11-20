/*************************************PROCEDURES*************************************/

/*zmena datumu sezeni*/
create procedure change_contact(player_id in number, new_contact in hrac.kontakt%type)
as
begin
  update hrac
  set kontakt = new_contact
  where id_player = player_id;
  if sql%notfound then
    dbms_output.put_line('hrac neexistuje');
  end if;
end;
/

/*zmena vlastnika postavy*/
create procedure change_char_owner(character_id in postava.id_character%type, new_owner in number)
as
begin
  update postava
  set id_player = new_owner
  where id_character = character_id;
  if sql%notfound then
    dbms_output.put_line('postava neexistuje');
  end if;
end;
/

/*************************************PROCEDURES*************************************/

--select * from hrac;
--select * from postava;
execute change_char_owner(9, 10);

execute change_contact(1, 'oliv.rudolf@gmail.com');