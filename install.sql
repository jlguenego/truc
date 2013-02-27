#Ma base de données : 

CREATE TABLE event(
        id                    Integer NOT NULL ,
        title                 Varchar(25) NOT NULL ,
        content               Text NOT NULL ,
        event_date            Int NOT NULL ,
        nbr_person_wanted     Int NOT NULL ,
        nbr_person_registered Int NOT NULL ,
        id_user               Int NOT NULL ,
        PRIMARY KEY (id)
)ENGINE=InnoDB;

CREATE TABLE user(
        id        Int NOT NULL ,
        login     Varchar(25) NOT NULL ,
        password  Varchar(120) NOT NULL ,
        email     Varchar(25) NOT NULL ,
        lastname  Varchar(25) NOT NULL ,
        firstname Varchar(25) NOT NULL ,
        role      Int NOT NULL ,
        PRIMARY KEY (id)
)ENGINE=InnoDB;

CREATE TABLE rate(
        id       Int NOT NULL ,
        label    Varchar(25) NOT NULL ,
        amount   Int NOT NULL ,
        id_event Integer NOT NULL ,
        PRIMARY KEY (id)
)ENGINE=InnoDB;

CREATE TABLE participate(
        id_user  Int NOT NULL ,
        id_event Integer NOT NULL ,
        PRIMARY KEY (id_user,id_event)
)ENGINE=InnoDB;

ALTER TABLE event ADD CONSTRAINT FK_event_id_user FOREIGN KEY (id_user) REFERENCES user(id);
ALTER TABLE rate ADD CONSTRAINT FK_rate_id_event FOREIGN KEY (id_event) REFERENCES event(id);
ALTER TABLE participate ADD CONSTRAINT FK_participate_id_user FOREIGN KEY (id_user) REFERENCES user(id);
ALTER TABLE participate ADD CONSTRAINT FK_participate_id_event FOREIGN KEY (id_event) REFERENCES event(id);
