#------------------------------------------------------------
#        Script MySQL.
#------------------------------------------------------------


CREATE TABLE event(
        id                Int NOT NULL ,
        created_t         Varchar (25) NOT NULL ,
        mod_t             Varchar (25) NOT NULL ,
        title             Varchar (255) NOT NULL ,
        organizer_name    Varchar (255) NOT NULL ,
        happening_t       Varchar (25) NOT NULL ,
        confirmation_t    Varchar (25) NOT NULL ,
        funding_needed    Decimal (25,2) NOT NULL ,
        funding_acquired  Decimal (25,2) NOT NULL ,
        link              Varchar (255) ,
        phone             Varchar (25) ,
        short_description Text NOT NULL ,
        long_description  Text ,
        type              Int NOT NULL ,
        status            Int NOT NULL ,
        publish_flag      Int NOT NULL ,
        flags             Int ,
        deal_name         Varchar (255) ,
        facebook_event_id Varchar (255) ,
        id_user           Int NOT NULL ,
        id_address        Int NOT NULL ,
        id_address1       Int NOT NULL ,
        PRIMARY KEY (id )
)ENGINE=InnoDB;


CREATE TABLE user(
        id                Int NOT NULL ,
        created_t         Varchar (25) NOT NULL ,
        mod_t             Varchar (25) NOT NULL ,
        email             Varchar (255) NOT NULL ,
        password          Varchar (255) NOT NULL ,
        lastname          Varchar (255) NOT NULL ,
        firstname         Varchar (255) NOT NULL ,
        role              Int NOT NULL ,
        activation_status Int NOT NULL ,
        activation_key    Varchar (255) ,
        locale            Varchar (25) ,
        token             Varchar (255) ,
        phone             Varchar (25) ,
        id_address        Int NOT NULL ,
        PRIMARY KEY (id )
)ENGINE=InnoDB;


CREATE TABLE ticket(
        id           Int NOT NULL ,
        created_t    Varchar (25) NOT NULL ,
        mod_t        Varchar (25) NOT NULL ,
        name         Varchar (255) NOT NULL ,
        type         Int NOT NULL ,
        max_quantity Int ,
        amount       Decimal (25,2) NOT NULL ,
        tax_rate     Decimal (25,2) NOT NULL ,
        start_t      Varchar (25) ,
        end_t        Varchar (25) ,
        description  Text NOT NULL ,
        id_event     Int NOT NULL ,
        PRIMARY KEY (id )
)ENGINE=InnoDB;


CREATE TABLE sequence(
        name    Varchar (255) NOT NULL ,
        current Int NOT NULL ,
        PRIMARY KEY (name )
)ENGINE=InnoDB;


CREATE TABLE bill(
        id           Int NOT NULL ,
        created_t    Varchar (25) NOT NULL ,
        mod_t        Varchar (25) NOT NULL ,
        flags        Int ,
        label        Varchar (255) NOT NULL ,
        payment_info Text ,
        total_ht     Decimal (25,2) NOT NULL ,
        total_tax    Decimal (25,2) NOT NULL ,
        total_ttc    Decimal (25,2) NOT NULL ,
        username     Varchar (255) NOT NULL ,
        vat          Varchar (255) ,
        status       Int NOT NULL ,
        type         Int NOT NULL ,
        is_for       Int ,
        id_user      Int NOT NULL ,
        id_event     Int NOT NULL ,
        id_address   Int NOT NULL ,
        PRIMARY KEY (id )
)ENGINE=InnoDB;


CREATE TABLE item(
        id                 Int NOT NULL ,
        event_name         Varchar (255) ,
        event_rate_name    Varchar (255) ,
        event_rate_amount  Decimal (25,2) ,
        event_rate_tax     Decimal (25,2) NOT NULL ,
        quantity           Int NOT NULL ,
        total_ht           Decimal (25,2) NOT NULL ,
        total_tax          Decimal (25,2) NOT NULL ,
        total_ttc          Decimal (25,2) NOT NULL ,
        attendee_firstname Varchar (255) ,
        attendee_lastname  Varchar (255) ,
        attendee_title     Varchar (25) ,
        id_bill            Int NOT NULL ,
        id_ticket          Int NOT NULL ,
        PRIMARY KEY (id )
)ENGINE=InnoDB;


CREATE TABLE guest(
        id        Int NOT NULL ,
        created_t Varchar (25) ,
        mod_t     Varchar (25) ,
        email     Varchar (255) ,
        PRIMARY KEY (id )
)ENGINE=InnoDB;


CREATE TABLE interaction(
        id               Int NOT NULL ,
        created_t        Varchar (25) ,
        mod_t            Varchar (25) ,
        type             Int ,
        id_guest         Int ,
        id_advertisement Int ,
        PRIMARY KEY (id )
)ENGINE=InnoDB;


CREATE TABLE advertisement(
        id        Int NOT NULL ,
        created_t Varchar (25) ,
        mod_t     Varchar (25) ,
        name      Varchar (255) ,
        content_h Text ,
        status    Int ,
        id_event  Int NOT NULL ,
        PRIMARY KEY (id )
)ENGINE=InnoDB;


CREATE TABLE task(
        id          Int NOT NULL ,
        created_t   Varchar (25) ,
        mod_t       Varchar (25) ,
        start_t     Varchar (25) ,
        status      Int NOT NULL ,
        description Varchar (255) ,
        command     Text ,
        parameters  Text ,
        error_msg   Text ,
        id_event    Int NOT NULL ,
        PRIMARY KEY (id )
)ENGINE=InnoDB;


CREATE TABLE address(
        id                          Int NOT NULL ,
        created_t                   Varchar (255) NOT NULL ,
        mod_t                       Varchar (255) NOT NULL ,
        address                     Varchar (255) ,
        lat                         Varchar (255) ,
        lng                         Varchar (255) ,
        street_number               Varchar (255) ,
        route                       Varchar (255) ,
        postal_code                 Varchar (255) ,
        locality                    Varchar (255) ,
        administrative_area_level_2 Varchar (255) ,
        administrative_area_level_1 Varchar (255) ,
        country                     Varchar (255) ,
        PRIMARY KEY (id )
)ENGINE=InnoDB;


CREATE TABLE event_guest(
        id_guest Int NOT NULL ,
        id_event Int NOT NULL ,
        PRIMARY KEY (id_guest ,id_event )
)ENGINE=InnoDB;

ALTER TABLE event ADD CONSTRAINT FK_event_id_user FOREIGN KEY (id_user) REFERENCES user(id);
ALTER TABLE event ADD CONSTRAINT FK_event_id_address FOREIGN KEY (id_address) REFERENCES address(id);
ALTER TABLE event ADD CONSTRAINT FK_event_id_address1 FOREIGN KEY (id_address1) REFERENCES address(id);
ALTER TABLE user ADD CONSTRAINT FK_user_id_address FOREIGN KEY (id_address) REFERENCES address(id);
ALTER TABLE ticket ADD CONSTRAINT FK_ticket_id_event FOREIGN KEY (id_event) REFERENCES event(id);
ALTER TABLE bill ADD CONSTRAINT FK_bill_id_user FOREIGN KEY (id_user) REFERENCES user(id);
ALTER TABLE bill ADD CONSTRAINT FK_bill_id_event FOREIGN KEY (id_event) REFERENCES event(id);
ALTER TABLE bill ADD CONSTRAINT FK_bill_id_address FOREIGN KEY (id_address) REFERENCES address(id);
ALTER TABLE item ADD CONSTRAINT FK_item_id_bill FOREIGN KEY (id_bill) REFERENCES bill(id);
ALTER TABLE item ADD CONSTRAINT FK_item_id_ticket FOREIGN KEY (id_ticket) REFERENCES ticket(id);
ALTER TABLE interaction ADD CONSTRAINT FK_interaction_id_guest FOREIGN KEY (id_guest) REFERENCES guest(id);
ALTER TABLE interaction ADD CONSTRAINT FK_interaction_id_advertisement FOREIGN KEY (id_advertisement) REFERENCES advertisement(id);
ALTER TABLE advertisement ADD CONSTRAINT FK_advertisement_id_event FOREIGN KEY (id_event) REFERENCES event(id);
ALTER TABLE task ADD CONSTRAINT FK_task_id_event FOREIGN KEY (id_event) REFERENCES event(id);
ALTER TABLE event_guest ADD CONSTRAINT FK_event_guest_id_guest FOREIGN KEY (id_guest) REFERENCES guest(id);
ALTER TABLE event_guest ADD CONSTRAINT FK_event_guest_id_event FOREIGN KEY (id_event) REFERENCES event(id);
