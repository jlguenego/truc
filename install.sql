CREATE TABLE event(
	id                    Integer NOT NULL ,
	title                 Varchar(25) NOT NULL ,
	content               Text NOT NULL ,
	event_date            TimeStamp NOT NULL ,
	nbr_person_wanted     Int NOT NULL ,
	nbr_person_registered Int NOT NULL ,
	PRIMARY KEY (id)
)ENGINE=InnoDB;

CREATE TABLE user(
	id       Int NOT NULL ,
	name     Varchar(25) NOT NULL ,
	lastname Varchar(25) NOT NULL ,
	email    Varchar(25) NOT NULL ,
	login    Varchar(25) NOT NULL ,
	password Varchar(25) NOT NULL ,
	admin 	Boolean NOT NULL DEFAULT FALSE,
	PRIMARY KEY (id)
)ENGINE=InnoDB;

CREATE TABLE participate(
        id_user  Int NOT NULL ,
        id_event Integer NOT NULL ,
        PRIMARY KEY (id_user,id_event)
)ENGINE=InnoDB;

ALTER TABLE participate ADD CONSTRAINT FK_participate_id_user FOREIGN KEY (id_user) REFERENCES user(id);
ALTER TABLE participate ADD CONSTRAINT FK_participate_id_event FOREIGN KEY (id_event) REFERENCES event(id);