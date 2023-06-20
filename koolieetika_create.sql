-- Created by Vertabelo (http://vertabelo.com)
-- Last modification date: 2023-06-20 12:08:21.554

-- tables
-- Table: dokument
CREATE TABLE dokument (
    id int  NOT NULL AUTO_INCREMENT,
    tekstiloik_id int  NOT NULL,
    nimi varchar(100)  NOT NULL,
    CONSTRAINT dokument_pk PRIMARY KEY (id)
);

-- Table: enesetest
CREATE TABLE enesetest (
    id int  NOT NULL AUTO_INCREMENT,
    tekstiloik_id int  NOT NULL,
    juhtum blob  NOT NULL,
    tugimaterjal blob  NULL,
    CONSTRAINT enesetest_pk PRIMARY KEY (id)
);

-- Table: haldaja
CREATE TABLE haldaja (
    id int  NOT NULL AUTO_INCREMENT,
    username varchar(50)  NOT NULL,
    password varchar(100)  NOT NULL,
    email varchar(50)  NOT NULL,
    kustutatud datetime  NULL,
    CONSTRAINT haldaja_pk PRIMARY KEY (id)
);

-- Table: kategooria
CREATE TABLE kategooria (
    id int  NOT NULL AUTO_INCREMENT,
    nimetus varchar(30)  NOT NULL,
    keel varchar(1)  NOT NULL,
    CONSTRAINT kategooria_pk PRIMARY KEY (id)
);

-- Table: materjal
CREATE TABLE materjal (
    id int  NOT NULL AUTO_INCREMENT,
    tekstiloik_id int  NOT NULL,
    tekst blob  NOT NULL,
    CONSTRAINT materjal_pk PRIMARY KEY (id)
);

-- Table: tekstiloik
CREATE TABLE tekstiloik (
    id int  NOT NULL AUTO_INCREMENT,
    kategooria_id int  NOT NULL,
    haldaja_id int  NOT NULL,
    pealkiri varchar(50)  NULL,
    jarjestus int  NOT NULL,
    lisatud timestamp  NOT NULL,
    muudetud datetime  NULL,
    kustutatud datetime  NULL,
    avalik bool  NULL,
    liik varchar(1)  NOT NULL,
    CONSTRAINT tekstiloik_pk PRIMARY KEY (id)
);

-- Table: vastusevali
CREATE TABLE vastusevali (
    id int  NOT NULL AUTO_INCREMENT,
    enesetest_id int  NOT NULL,
    kysimus varchar(1000)  NOT NULL,
    vastus varchar(1000)  NULL,
    jarjekord int  NOT NULL,
    kustutatud datetime  NULL,
    CONSTRAINT vastusevali_pk PRIMARY KEY (id)
) COMMENT 'Ühe testküsimuse juures võib olla mitu vastusevälja.';

-- foreign keys
-- Reference: alamkategooria_haldaja (table: tekstiloik)
ALTER TABLE tekstiloik ADD CONSTRAINT alamkategooria_haldaja FOREIGN KEY alamkategooria_haldaja (haldaja_id)
    REFERENCES haldaja (id);

-- Reference: alamkategooria_kategooria (table: tekstiloik)
ALTER TABLE tekstiloik ADD CONSTRAINT alamkategooria_kategooria FOREIGN KEY alamkategooria_kategooria (kategooria_id)
    REFERENCES kategooria (id);

-- Reference: dokument_tekstiloik (table: dokument)
ALTER TABLE dokument ADD CONSTRAINT dokument_tekstiloik FOREIGN KEY dokument_tekstiloik (tekstiloik_id)
    REFERENCES tekstiloik (id);

-- Reference: enesetest_tekstiloik (table: enesetest)
ALTER TABLE enesetest ADD CONSTRAINT enesetest_tekstiloik FOREIGN KEY enesetest_tekstiloik (tekstiloik_id)
    REFERENCES tekstiloik (id);

-- Reference: materjal_alamkategooria (table: materjal)
ALTER TABLE materjal ADD CONSTRAINT materjal_alamkategooria FOREIGN KEY materjal_alamkategooria (tekstiloik_id)
    REFERENCES tekstiloik (id);

-- Reference: vastusevali_enesetest (table: vastusevali)
ALTER TABLE vastusevali ADD CONSTRAINT vastusevali_enesetest FOREIGN KEY vastusevali_enesetest (enesetest_id)
    REFERENCES enesetest (id);

-- End of file.

