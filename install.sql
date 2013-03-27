#Ma base de données :

CREATE TABLE event(
        id                Int NOT NULL ,
        created_t         Varchar(25) NOT NULL ,
        mod_t             Varchar(25) NOT NULL ,
        title             Varchar(255) NOT NULL ,
        open_t            Varchar(25) NOT NULL ,
        confirmation_t    Varchar(25) NOT NULL ,
        happening_t       Varchar(25) NOT NULL ,
        funding_needed    Decimal (25,2) NOT NULL ,
        funding_acquired  Decimal (25,2) NOT NULL ,
        location          Varchar(25) NOT NULL ,
        link              Varchar(255),
        short_description Varchar(255) NOT NULL ,
        long_description  Text,
        nominative        Bool NOT NULL ,
        status            Int NOT NULL ,
        publish_flag      Int NOT NULL ,
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
        street            Varchar(255) NOT NULL ,
        zip               Varchar(255) NOT NULL ,
        city              Varchar(255) NOT NULL ,
        country           Varchar(255) NOT NULL ,
        state             Varchar(255),
        token             Varchar(255),
        PRIMARY KEY (id)
)ENGINE=InnoDB;

CREATE TABLE rate(
        id       Int NOT NULL ,
        label    Varchar(25) NOT NULL ,
        amount   Decimal (25,2) NOT NULL ,
        tax_rate Decimal (25,2) NOT NULL ,
        id_event Int NOT NULL ,
        PRIMARY KEY (id)
)ENGINE=InnoDB;

CREATE TABLE sequence(
        name    Varchar(255) NOT NULL ,
        current Int NOT NULL ,
        PRIMARY KEY (name)
)ENGINE=InnoDB;

CREATE TABLE bill(
        id        Int NOT NULL ,
        created_t Varchar(25) NOT NULL ,
        total_ht  Decimal (25,2) NOT NULL ,
        total_tax Decimal (25,2) NOT NULL ,
        total_ttc Decimal (25,2) NOT NULL ,
        label     Varchar(255) NOT NULL ,
        username  Varchar(255) NOT NULL ,
        address   Varchar(255) NOT NULL ,
        status    Int NOT NULL ,
        type      Int NOT NULL ,
        id_user   Int NOT NULL ,
        id_event  Int NOT NULL ,
        PRIMARY KEY (id)
)ENGINE=InnoDB;

CREATE TABLE item(
        id                    Int NOT NULL ,
        event_name            Varchar(255) NOT NULL ,
        event_rate_name       Varchar(255) NOT NULL ,
        event_rate_amount     Decimal (25,2) NOT NULL ,
        event_rate_tax        Decimal (25,2) NOT NULL ,
        quantity              Int NOT NULL ,
        total_ht              Decimal (25,2) NOT NULL ,
        total_tax             Decimal (25,2) NOT NULL ,
        total_ttc             Decimal (25,2) NOT NULL ,
        participant_firstname Varchar(255),
        participant_lastname  Varchar(255),
        participant_title     Varchar(25),
        id_bill               Int NOT NULL ,
        PRIMARY KEY (id)
)ENGINE=InnoDB;

ALTER TABLE event ADD CONSTRAINT FK_event_id_user FOREIGN KEY (id_user) REFERENCES user(id);
ALTER TABLE rate ADD CONSTRAINT FK_rate_id_event FOREIGN KEY (id_event) REFERENCES event(id);
ALTER TABLE bill ADD CONSTRAINT FK_bill_id_user FOREIGN KEY (id_user) REFERENCES user(id);
ALTER TABLE bill ADD CONSTRAINT FK_bill_id_event FOREIGN KEY (id_event) REFERENCES event(id);
ALTER TABLE item ADD CONSTRAINT FK_item_id_bill FOREIGN KEY (id_bill) REFERENCES bill(id);
