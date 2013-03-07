#Ma base de données : 

CREATE TABLE event(
        id                Int NOT NULL ,
        created_t         Varchar(25) NOT NULL ,
        mod_t             Varchar(25) NOT NULL ,
        title             Varchar(25) NOT NULL ,
        event_deadline    Varchar(25) NOT NULL ,
        event_date        Varchar(25) NOT NULL ,
        funding_wanted    Int NOT NULL ,
        funding_acquired  Int NOT NULL ,
        location          Varchar(25) NOT NULL ,
        link              Varchar(255),
        short_description Varchar(255) NOT NULL ,
        long_description  Text,
        id_user           Int NOT NULL ,
        PRIMARY KEY (id)
);

CREATE TABLE user(
        id                Int NOT NULL ,
        created_t         Varchar(25) NOT NULL ,
        mod_t             Varchar(25) NOT NULL ,
        login             Varchar(25) NOT NULL ,
        password          Varchar(120) NOT NULL ,
        email             Varchar(25) NOT NULL ,
        lastname          Varchar(25) NOT NULL ,
        firstname         Varchar(25) NOT NULL ,
        role              Int NOT NULL ,
        activation_status Int NOT NULL ,
        activation_key    Varchar(255),
        PRIMARY KEY (id)
);

CREATE TABLE rate(
        id       Int NOT NULL ,
        label    Varchar(25) NOT NULL ,
        amount   Int NOT NULL ,
        tax_rate Decimal (25,2) NOT NULL ,
        id_event Int NOT NULL ,
        PRIMARY KEY (id)
);

CREATE TABLE participate(
        id_user  Int NOT NULL ,
        id_event Int NOT NULL ,
        PRIMARY KEY (id_user,id_event)
);

ALTER TABLE event ADD CONSTRAINT FK_event_id_user FOREIGN KEY (id_user) REFERENCES user(id);
ALTER TABLE rate ADD CONSTRAINT FK_rate_id_event FOREIGN KEY (id_event) REFERENCES event(id);
ALTER TABLE participate ADD CONSTRAINT FK_participate_id_user FOREIGN KEY (id_user) REFERENCES user(id);
ALTER TABLE participate ADD CONSTRAINT FK_participate_id_event FOREIGN KEY (id_event) REFERENCES event(id);
