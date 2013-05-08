#------------------------------------------------------------
#        Script MySQL.
#------------------------------------------------------------


CREATE TABLE event(
        id                Int NOT NULL  ,
        created_t         Varchar (25) NOT NULL  ,
        mod_t             Varchar (25) NOT NULL  ,
        title             Varchar (255) NOT NULL  ,
        organizer_name    Varchar (255) NOT NULL  ,
        happening_t       Varchar (25) NOT NULL  ,
        confirmation_t    Varchar (25) NOT NULL  ,
        funding_needed    Decimal (25,2) NOT NULL  ,
        funding_acquired  Decimal (25,2) NOT NULL  ,
        location          Varchar (255) NOT NULL  ,
        link              Varchar (255) ,
        phone             Varchar (25) ,
        short_description Text NOT NULL  ,
        long_description  Text ,
        type              Int NOT NULL  ,
        status            Int NOT NULL  ,
        publish_flag      Int NOT NULL  ,
        flags             Int ,
        id_user           Int NOT NULL  ,
        PRIMARY KEY (id )
)ENGINE=InnoDB;


CREATE TABLE user(
        id                Int NOT NULL  ,
        created_t         Varchar (25) NOT NULL  ,
        mod_t             Varchar (25) NOT NULL  ,
        email             Varchar (255) NOT NULL  ,
        password          Varchar (255) NOT NULL  ,
        lastname          Varchar (255) NOT NULL  ,
        firstname         Varchar (255) NOT NULL  ,
        role              Int NOT NULL  ,
        activation_status Int NOT NULL  ,
        activation_key    Varchar (255) ,
        street            Varchar (255) ,
        zip               Varchar (255) ,
        city              Varchar (255) ,
        country           Varchar (255) ,
        state             Varchar (255) ,
        locale            Varchar (25) ,
        token             Varchar (255) ,
        phone             Varchar (25) ,
        PRIMARY KEY (id )
)ENGINE=InnoDB;


CREATE TABLE ticket(
        id           Int NOT NULL  ,
        created_t    Varchar (25) NOT NULL  ,
        mod_t        Varchar (25) NOT NULL  ,
        name         Varchar (255) NOT NULL  ,
        type         Int NOT NULL  ,
        max_quantity Int ,
        amount       Decimal (25,2) NOT NULL  ,
        tax_rate     Decimal (25,2) NOT NULL  ,
        start_t      Varchar (25) ,
        end_t        Varchar (25) ,
        description  Text NOT NULL  ,
        id_event     Int NOT NULL  ,
        PRIMARY KEY (id )
)ENGINE=InnoDB;


CREATE TABLE sequence(
        name    Varchar (255) NOT NULL  ,
        current Int NOT NULL  ,
        PRIMARY KEY (name )
)ENGINE=InnoDB;


CREATE TABLE bill(
        id           Int NOT NULL  ,
        created_t    Varchar (25) NOT NULL  ,
        label        Varchar (255) NOT NULL  ,
        payment_info Varchar (255) ,
        total_ht     Decimal (25,2) NOT NULL  ,
        total_tax    Decimal (25,2) NOT NULL  ,
        total_ttc    Decimal (25,2) NOT NULL  ,
        username     Varchar (255) NOT NULL  ,
        address      Text NOT NULL  ,
        vat          Varchar (255) ,
        status       Int NOT NULL  ,
        type         Int NOT NULL  ,
        id_user      Int NOT NULL  ,
        id_event     Int NOT NULL  ,
        PRIMARY KEY (id )
)ENGINE=InnoDB;


CREATE TABLE item(
        id                 Int NOT NULL  ,
        event_name         Varchar (255) NOT NULL  ,
        event_rate_name    Varchar (255) NOT NULL  ,
        event_rate_amount  Decimal (25,2) NOT NULL  ,
        event_rate_tax     Decimal (25,2) NOT NULL  ,
        quantity           Int NOT NULL  ,
        total_ht           Decimal (25,2) NOT NULL  ,
        total_tax          Decimal (25,2) NOT NULL  ,
        total_ttc          Decimal (25,2) NOT NULL  ,
        attendee_firstname Varchar (255) ,
        attendee_lastname  Varchar (255) ,
        attendee_title     Varchar (25) ,
        id_bill            Int NOT NULL  ,
        PRIMARY KEY (id )
)ENGINE=InnoDB;


CREATE TABLE guest(
        id        Int NOT NULL  ,
        created_t Varchar (25) ,
        mod_t     Varchar (25) ,
        email     Varchar (255) ,
        PRIMARY KEY (id )
)ENGINE=InnoDB;


CREATE TABLE interaction(
        id              Int NOT NULL  ,
        created_t       Varchar (25) ,
        mod_t           Varchar (25) ,
        type            Int ,
        id_guest        Int ,
        id_advertisment Int ,
        PRIMARY KEY (id )
)ENGINE=InnoDB;


CREATE TABLE advertisment(
        id        Int NOT NULL  ,
        created_t Varchar (25) ,
        mod_t     Varchar (25) ,
        content   Text ,
        id_event  Int NOT NULL  ,
        PRIMARY KEY (id )
)ENGINE=InnoDB;


CREATE TABLE event_guest(
        id_guest Int NOT NULL  ,
        id_event Int NOT NULL  ,
        PRIMARY KEY (id_guest ,id_event )
)ENGINE=InnoDB;

ALTER TABLE event ADD CONSTRAINT FK_event_id_user FOREIGN KEY (id_user) REFERENCES user(id);
ALTER TABLE ticket ADD CONSTRAINT FK_ticket_id_event FOREIGN KEY (id_event) REFERENCES event(id);
ALTER TABLE bill ADD CONSTRAINT FK_bill_id_user FOREIGN KEY (id_user) REFERENCES user(id);
ALTER TABLE bill ADD CONSTRAINT FK_bill_id_event FOREIGN KEY (id_event) REFERENCES event(id);
ALTER TABLE item ADD CONSTRAINT FK_item_id_bill FOREIGN KEY (id_bill) REFERENCES bill(id);
ALTER TABLE interaction ADD CONSTRAINT FK_interaction_id_guest FOREIGN KEY (id_guest) REFERENCES guest(id);
ALTER TABLE interaction ADD CONSTRAINT FK_interaction_id_advertisment FOREIGN KEY (id_advertisment) REFERENCES advertisment(id);
ALTER TABLE advertisment ADD CONSTRAINT FK_advertisment_id_event FOREIGN KEY (id_event) REFERENCES event(id);
ALTER TABLE event_guest ADD CONSTRAINT FK_event_guest_id_guest FOREIGN KEY (id_guest) REFERENCES guest(id);
ALTER TABLE event_guest ADD CONSTRAINT FK_event_guest_id_event FOREIGN KEY (id_event) REFERENCES event(id);
