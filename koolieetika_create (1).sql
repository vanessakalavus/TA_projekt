-- Created by Vertabelo (http://vertabelo.com)
-- Last modification date: 2023-06-19 18:10:36.445

-- tables
-- Table: category
CREATE TABLE category (
    id int  NOT NULL AUTO_INCREMENT,
    name varchar(30)  NOT NULL,
    language varchar(1)  NOT NULL,
    CONSTRAINT category_pk PRIMARY KEY (id)
);

-- Table: document
CREATE TABLE document (
    id int  NOT NULL AUTO_INCREMENT,
    page_section_id int  NOT NULL,
    name varchar(100)  NOT NULL,
    CONSTRAINT document_pk PRIMARY KEY (id)
);

-- Table: materjal
CREATE TABLE materjal (
    id int  NOT NULL AUTO_INCREMENT,
    page_section_id int  NOT NULL,
    tekst blob  NOT NULL,
    CONSTRAINT materjal_pk PRIMARY KEY (id)
);

-- Table: page_section
CREATE TABLE page_section (
    id int  NOT NULL AUTO_INCREMENT,
    category_id int  NOT NULL,
    user_id int  NOT NULL,
    title varchar(50)  NULL,
    `order` int  NOT NULL,
    added timestamp  NOT NULL,
    changed datetime  NULL,
    deleted datetime  NULL,
    public bool  NULL,
    type varchar(1)  NOT NULL,
    CONSTRAINT page_section_pk PRIMARY KEY (id)
);

-- Table: self_test
CREATE TABLE self_test (
    id int  NOT NULL AUTO_INCREMENT,
    page_section_id int  NOT NULL,
    `case` blob  NOT NULL,
    support blob  NULL,
    CONSTRAINT self_test_pk PRIMARY KEY (id)
);

-- Table: users
CREATE TABLE users (
    id int  NOT NULL AUTO_INCREMENT,
    username varchar(50)  NOT NULL,
    password varchar(100)  NOT NULL,
    email varchar(50)  NOT NULL,
    CONSTRAINT users_pk PRIMARY KEY (id)
);

-- Table: vastusevali
CREATE TABLE vastusevali (
    id int  NOT NULL AUTO_INCREMENT,
    self_test_id int  NOT NULL,
    question varchar(1000)  NOT NULL,
    answer varchar(1000)  NULL,
    deleted datetime  NULL,
    CONSTRAINT vastusevali_pk PRIMARY KEY (id)
) COMMENT 'Ühe testküsimuse juures võib olla mitu vastusevälja.';

-- foreign keys
-- Reference: alamkategooria_haldaja (table: page_section)
ALTER TABLE page_section ADD CONSTRAINT alamkategooria_haldaja FOREIGN KEY alamkategooria_haldaja (user_id)
    REFERENCES users (id);

-- Reference: alamkategooria_kategooria (table: page_section)
ALTER TABLE page_section ADD CONSTRAINT alamkategooria_kategooria FOREIGN KEY alamkategooria_kategooria (category_id)
    REFERENCES category (id);

-- Reference: dokument_tekstiloik (table: document)
ALTER TABLE document ADD CONSTRAINT dokument_tekstiloik FOREIGN KEY dokument_tekstiloik (page_section_id)
    REFERENCES page_section (id);

-- Reference: enesetest_tekstiloik (table: self_test)
ALTER TABLE self_test ADD CONSTRAINT enesetest_tekstiloik FOREIGN KEY enesetest_tekstiloik (page_section_id)
    REFERENCES page_section (id);

-- Reference: materjal_alamkategooria (table: materjal)
ALTER TABLE materjal ADD CONSTRAINT materjal_alamkategooria FOREIGN KEY materjal_alamkategooria (page_section_id)
    REFERENCES page_section (id);

-- Reference: vastusevali_enesetest (table: vastusevali)
ALTER TABLE vastusevali ADD CONSTRAINT vastusevali_enesetest FOREIGN KEY vastusevali_enesetest (self_test_id)
    REFERENCES self_test (id);

-- End of file.

