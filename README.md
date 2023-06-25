# TA_projekt
Koolieetika veebileht koos sisuhaldusplatvormiga
Lühikirjeldus ning eesmärk:
Projekt on loodud Tallinna Ülikooli esimese kursuse tudengite poolt “Tarkvara arenduse projekt” aine raames. Aine eesmärgiks on anda tudengitele kogemus tiimina rakenduse/veebilehe loomise algusest lõpuni loomisest. Projekti autoriteks on Karl Rebane, Märten Treier, Kaia Mia Kalda ja Vanessa Kalavus.

Eesmärgiks on luua koolieetika teemasid kajastav sisuhaldusplatvorm, mis on lehe haldajale mugav ja lihtne kasutada. Lehekülg peab võimaldama külastajatel eneseteste sooritada ning enda vastuseid salvestada. Haldaja saab lisada materjale, teste, (haldajaõigustega) kasutajaid. Muuta materjalide, testide - sisu, järjekorda, seisundit ning muuta kasutajaandmeid. Samuti saab haldaja muuta oma kontaktandmeid ja parooli. Projekti tellijal on hetkel kasutusel lehekülg Wix’i platvormil, kus testide lisamine/sooritamine pole võimalik, sellest vajadus-huvi uue lahenduse vastu. Meie loodud lahenduses on võimalik veebilehte ka inglisekeelseks muuta.

Veebilehel kasutasime nelja erinevat keelt, milleks on HTML5, PHP 8.0.0, CSS3 ja JavaScript 13th edition. Andmebaasina kasutasime MariaDB-d.

Selleks, et veebilehte paigaldada, tuleb kõigepealt leida sobiv veebimajutus, mis toetaks eelmainitud keeli. Veenduge, et teie valitud veebimajutusplatvorm toetab MySQL andmebaase.

Järgnevalt tuleks teha andmebaas. Andmebaasi lisamise võimalus peaks nähtavale tekkima kas juhtpaneeli (control panel) või andmebaasi haldamissektsiooni (database management section) valiku alt. Kategooriate nimetused võivad varieeruda.

Andmebaasi lisamisel on vaja määrata serveri host, kasutajanimi, parool ja andmebaasi nimi. Määra endale kasutajanimi ja genereeri endale turvaline parool. Jäta kasutajanimi meelde ning kopeeri genereeritud parool endale turvalisse kohta. Andmebaasi nimeks määrake if22_koolieetika2.

Nüüd lisage config2.php nimeline fail juurkataloogi. Ava fail ning muuda $server_user_name ja $server_password väärtused vastavalt oma lisatud kasutajanimele ja genereeritud salasõnale. Kontrollige, et vajalikud autentimissõned oleksid korrektsed ja jutumärkides.

NB!: Juhul, kui määrasite andmebaasi nimeks midagi muud kui "if22_koolieetika2", peate ka config2.php failis muutma $database järel jutumärkidesse sisestatud nime oma määratud nime vastu.

Kui eelmainitud sammud on läbitud, tuleb järgmisena tekitada andmebaasi tabelid. Tabelite loomiseks vajalikud sql käsud on "koolieetika_create.sql" failis. Käsud saab kopeerida ning sisestada kasutades phpMyAdmin liidest, mis on saadaval enamus MySQL-i pakkuvatel veebimajutustel. Võimalus pääseda phpMyAdmin vaatele peaks olema leitav veebimajutusplatvormi juhtpaneelilt "Databases" või muu sarnase valiku alt. Sealt edasi leiate oma andmebaasi nime alt valiku "Impordi" (või "Import", sõltuvalt keelest). Valige või lohistage koolieetika_create.sql fail lehele. Ülejäänud sätteid pole vaja muuta. Vajutage "Impordi" nupule ning peaksite saama teate, et skripti käivitamine õnnestus. 

Andmetabeleid ja nende relatsioone visualiseerib järgnev Vertabelo andmebaasimudel: ![db final](https://github.com/vanessakalavus/TA_projekt/assets/115349223/0e253d46-0d68-45b5-b5a1-ea9e3a563cdb)

Järgnevalt tuleks config2.php failiga samasse kausta luua uus kaust nimega "public_html". Public_html kausta tuleb luua uus kataloog/kaust nimega "koolieetika". Koolieetika kausta lisage kõik githubi repo failid, st. php failid ja css, js ning classes kaustad. 

Pildid rakendusest: 

sisselogimise leht: 
![Screenshot 2023-06-20 at 13-07-58 Haldaja sisselogimine](https://github.com/vanessakalavus/TA_projekt/assets/115349223/6691f5d6-2150-4c03-8270-f60989c2455b)

Haldaja paneel:
![Screenshot 2023-06-20 at 13-08-20 Haldaja paneel](https://github.com/vanessakalavus/TA_projekt/assets/115349223/42ce817a-de25-4875-9a96-ac599fd4e571)

Sisuhaldusleht:
![Screenshot 2023-06-20 at 13-08-29 Sisuhaldus](https://github.com/vanessakalavus/TA_projekt/assets/115349223/5dba6cae-2cd7-4b86-9ccc-0db6019f8e6f)

Loo postitus leht:
![Screenshot 2023-06-20 at 13-08-47 Loo postitus](https://github.com/vanessakalavus/TA_projekt/assets/115349223/ee90d497-66e0-40d6-b8f4-75086a6a6541)

Enesetesti loomine:
![Screenshot 2023-06-20 at 13-09-08 Enesetesti loomine](https://github.com/vanessakalavus/TA_projekt/assets/115349223/856f01c7-7938-4604-908c-44a635ac84d6)

Kontaktandmete muutmine:
![Screenshot 2023-06-20 at 13-09-25 Muuda kontaktandmeid](https://github.com/vanessakalavus/TA_projekt/assets/115349223/4ee3b79e-7bb7-4f46-a6eb-db010783ee6a)

Haldaja parooli muutmine
![Screenshot 2023-06-20 at 13-09-52 Haldaja parooli muutmine](https://github.com/vanessakalavus/TA_projekt/assets/115349223/655a4b26-7212-4079-9cae-7294aa110a56)

Kontohaldus:
![Screenshot 2023-06-20 at 13-10-25 Kontod](https://github.com/vanessakalavus/TA_projekt/assets/115349223/a2553ead-bea9-46bd-8ed5-54541eaf428f)

Uue kasutaja lisamine
![Screenshot 2023-06-20 at 13-10-35 Uue kasutaja lisamine](https://github.com/vanessakalavus/TA_projekt/assets/115349223/f23b47db-1514-46af-a389-1a3c6a3acebc)

Pildid avalehest:
![Screenshot 2023-06-20 at 14-04-40 Koolieetika](https://github.com/vanessakalavus/TA_projekt/assets/115349223/af5e4758-cec9-46d3-8102-91ceb5c3e9d2)
![Screenshot 2023-06-20 at 14-04-25 Koolieetika](https://github.com/vanessakalavus/TA_projekt/assets/115349223/8a548958-b901-49a4-ab24-012defbd7acc)
![Screenshot 2023-06-20 at 14-04-34 Koolieetika](https://github.com/vanessakalavus/TA_projekt/assets/115349223/e12db8bd-c400-467b-915d-f76a8fce878c)

Pilt õpilaste lehest: 
![Screenshot 2023-06-20 at 14-04-59 Koolieetika](https://github.com/vanessakalavus/TA_projekt/assets/115349223/8a819f32-ce56-460a-8894-c817a064f9f0)
NB! õpilaste, õpetajate ja haridusjuhtide lehed on samasugused, ainult et kuvatakse erinev materjal. 

MIT licence:
[MIT licence.pdf](https://github.com/vanessakalavus/TA_projekt/files/11798993/MIT.licence.pdf)


