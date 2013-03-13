#Ma base de donn�es :

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
        nominative        Bool NOT NULL ,
        id_user           Int NOT NULL ,
        PRIMARY KEY (id)
)ENGINE=InnoDB;

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
        address           Varchar(255) NOT NULL ,
        PRIMARY KEY (id)
)ENGINE=InnoDB;

CREATE TABLE rate(
        id       Int NOT NULL ,
        label    Varchar(25) NOT NULL ,
        amount   Int NOT NULL ,
        tax_rate Decimal (25,2) NOT NULL ,
        id_event Int NOT NULL ,
        PRIMARY KEY (id)
)ENGINE=InnoDB;

CREATE TABLE sequence(
        name    Varchar(255) NOT NULL ,
        current Int NOT NULL ,
        PRIMARY KEY (name)
)ENGINE=InnoDB;

CREATE TABLE devis(
        id        Int NOT NULL ,
        created_t Varchar(25) NOT NULL ,
        total_ht  Int NOT NULL ,
        total_tax Int NOT NULL ,
        total_ttc Int NOT NULL ,
        label     Varchar(255) NOT NULL ,
        username  Varchar(255) NOT NULL ,
        address   Varchar(255) NOT NULL ,
        PRIMARY KEY (id)
)ENGINE=InnoDB;

CREATE TABLE devis_item(
        id                    Int NOT NULL ,
        event_name            Varchar(255) NOT NULL ,
        event_rate_name       Varchar(255) NOT NULL ,
        event_rate_amount     Int NOT NULL ,
        event_rate_tax        Int NOT NULL ,
        quantity              Int NOT NULL ,
        total_ht              Int NOT NULL ,
        total_tax             Int NOT NULL ,
        total_ttc             Int NOT NULL ,
        participant_firstname Varchar(255),
        participant_lastname  Varchar(255),
        participant_title     Varchar(25),
        id_devis              Int NOT NULL ,
        PRIMARY KEY (id)
)ENGINE=InnoDB;

CREATE TABLE participate(
        id_user  Int NOT NULL ,
        id_event Int NOT NULL ,
        PRIMARY KEY (id_user,id_event)
)ENGINE=InnoDB;

ALTER TABLE event ADD CONSTRAINT FK_event_id_user FOREIGN KEY (id_user) REFERENCES user(id);
ALTER TABLE rate ADD CONSTRAINT FK_rate_id_event FOREIGN KEY (id_event) REFERENCES event(id);
ALTER TABLE devis_item ADD CONSTRAINT FK_devis_item_id_devis FOREIGN KEY (id_devis) REFERENCES devis(id);
ALTER TABLE participate ADD CONSTRAINT FK_participate_id_user FOREIGN KEY (id_user) REFERENCES user(id);
ALTER TABLE participate ADD CONSTRAINT FK_participate_id_event FOREIGN KEY (id_event) REFERENCES event(id);
