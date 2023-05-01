UPDATE entries SET entry_class = 1000 WHERE entry_text LIKE 'Overførsel OVERSKYDENDE SKAT' ;
UPDATE entries SET entry_class = 1050 WHERE entry_text LIKE 'Overførsel Syddansk Universitet' ;
UPDATE entries SET entry_class = 1100 WHERE entry_text LIKE 'GS' ;
UPDATE entries SET entry_class = 1100 WHERE entry_text LIKE 'GrammarSoft' ;
UPDATE entries SET entry_class = 1100 WHERE entry_text LIKE 'Oqaasileriffik' ;
UPDATE entries SET entry_class = 1100 WHERE entry_text LIKE 'LG' ;
UPDATE entries SET entry_class = 1100 WHERE entry_text LIKE 'Surplus%' ;
UPDATE entries SET entry_class = 1100 WHERE entry_text LIKE 'B-skat' ;
UPDATE entries SET entry_class = 1100 WHERE entry_text LIKE 'Moms' ;
UPDATE entries SET entry_class = 1200 WHERE entry_text LIKE '%Børne- og Unge%' ;
UPDATE entries SET entry_class = 1250 WHERE entry_text LIKE 'MobilePay Maria%' ;
UPDATE entries SET entry_class = 1250 WHERE entry_text LIKE 'Overførsel' ;

UPDATE entries SET entry_class = 2050 WHERE entry_text LIKE '%JERVELUNDHAVEN%' ;
UPDATE entries SET entry_class = 2050 WHERE entry_text LIKE '%BOXIT%' ;
UPDATE entries SET entry_class = 2100 WHERE entry_text LIKE '%MODSTRØM%' ;
UPDATE entries SET entry_class = 2150 WHERE entry_text LIKE '%ADM.SERVICE%' ;

UPDATE entries SET entry_class = 3000 WHERE entry_text LIKE 'familyrider%' ;
UPDATE entries SET entry_class = 3000 WHERE entry_text LIKE 'Fri Bikeshop%' ;

UPDATE entries SET entry_class = 4100 WHERE entry_text LIKE 'Forsikringspræmie Liv%' ;
UPDATE entries SET entry_class = 4150 WHERE entry_text LIKE '%SYGEFORSIKRING%' ;
UPDATE entries SET entry_class = 4150 WHERE entry_text LIKE 'Overførsel danmark%' ;

-- 5650 Boligudstyr
UPDATE entries SET entry_class = 5650 WHERE entry_text LIKE 'IKEA%' ;
-- 5050 Dagligvarer
UPDATE entries SET entry_class = 5050 WHERE entry_text LIKE 'Føtex%' ;
UPDATE entries SET entry_class = 5050 WHERE entry_text LIKE 'Bilka%' ;
UPDATE entries SET entry_class = 5050 WHERE entry_text LIKE 'LIDL%' ;
UPDATE entries SET entry_class = 5050 WHERE entry_text LIKE 'Alfa+%' ;
UPDATE entries SET entry_class = 5050 WHERE entry_text LIKE 'REMA 1000%' ;
UPDATE entries SET entry_class = 5050 WHERE entry_text LIKE 'Meny %' ;
UPDATE entries SET entry_class = 5050 WHERE entry_text LIKE 'Dagrofa%' ;
UPDATE entries SET entry_class = 5050 WHERE entry_text LIKE 'TOFTEVEJENS%' ;
UPDATE entries SET entry_class = 5050 WHERE entry_text LIKE 'Coop %' ;
UPDATE entries SET entry_class = 5050 WHERE entry_text LIKE 'Netto%' ;
-- 5100 Restaurant / takeaway
UPDATE entries SET entry_class = 5100 WHERE entry_text LIKE 'Dalle Valle%' ;
UPDATE entries SET entry_class = 5100 WHERE entry_text LIKE '%Restaurant%' ;
UPDATE entries SET entry_class = 5100 WHERE entry_text LIKE 'DSB 7-Eleven%' ;
UPDATE entries SET entry_class = 5100 WHERE entry_text LIKE 'IKEA%IF%' ;
UPDATE entries SET entry_class = 5100 WHERE entry_text LIKE 'napoli%odense%' ;
UPDATE entries SET entry_class = 5100 WHERE entry_text LIKE 'Eurest%' ;
UPDATE entries SET entry_class = 5100 WHERE entry_text LIKE 'BURGER KING%' ;
-- 5150 Læge, medicin
UPDATE entries SET entry_class = 5150 WHERE entry_text LIKE 'Rosengården Ap %' ;
UPDATE entries SET entry_class = 5150 WHERE entry_text LIKE 'Niels Bohrs Al%' ;
UPDATE entries SET entry_class = 5150 WHERE entry_text LIKE 'Tandlæge%' ;
UPDATE entries SET entry_class = 5150 WHERE entry_text LIKE 'Faten%' ;
-- 5250 Personlig pleje
UPDATE entries SET entry_class = 5250 WHERE entry_text LIKE '%Lise Dyr%' ;
UPDATE entries SET entry_class = 5250 WHERE entry_text LIKE 'MATAS%' ;
UPDATE entries SET entry_class = 5250 WHERE entry_text LIKE 'Glitter%' ;
UPDATE entries SET entry_class = 5250 WHERE entry_text LIKE 'STRONG4LIFE%' ;
UPDATE entries SET entry_class = 5250 WHERE entry_text LIKE '%PIERCING%' ;
-- 5300 TV / streaming
UPDATE entries SET entry_class = 5300 WHERE entry_text LIKE 'Amazon Video%' ;
UPDATE entries SET entry_class = 5300 WHERE entry_text LIKE 'NETFLIX%' ;
UPDATE entries SET entry_class = 5300 WHERE entry_text LIKE 'cinemaxx%' ;
UPDATE entries SET entry_class = 5300 WHERE entry_text LIKE 'Patreon%' ;
UPDATE entries SET entry_class = 5300 WHERE entry_text LIKE 'TV2%' ;
UPDATE entries SET entry_class = 5300 WHERE entry_text LIKE 'ITUNES%' ;
UPDATE entries SET entry_class = 5300 WHERE entry_text LIKE '%CRUNCHYROLL%' ;
UPDATE entries SET entry_class = 5300 WHERE entry_text LIKE '%TWITCH%' ;
-- 5350 Mobiltelefon
UPDATE entries SET entry_class = 5350 WHERE entry_text LIKE '3 Danmark%' ;
UPDATE entries SET entry_class = 5350 WHERE entry_text LIKE '3´s%' ;
-- 5400 Internet
UPDATE entries SET entry_class = 5400 WHERE entry_text LIKE '%FASTSPEED%' ;
-- 5450 Fritid
UPDATE entries SET entry_class = 5450 WHERE entry_text LIKE 'Seden Rideklub%' ;
UPDATE entries SET entry_class = 5450 WHERE entry_text LIKE 'Horze%' ;
UPDATE entries SET entry_class = 5450 WHERE entry_text LIKE 'Hööks%' ;
UPDATE entries SET entry_class = 5450 WHERE entry_text LIKE 'shop.dds.dk%' ;
UPDATE entries SET entry_class = 5450 WHERE entry_text LIKE 'PANDURO%' ;
UPDATE entries SET entry_class = 5450 WHERE entry_text LIKE 'Søstrene grene%' ;
-- 5500 Transport
UPDATE entries SET entry_class = 5500 WHERE entry_text LIKE 'TAXA %' ;
UPDATE entries SET entry_class = 5500 WHERE entry_text LIKE 'Rejsekort%' ;
UPDATE entries SET entry_class = 5500 WHERE entry_text LIKE '%DSB APP%' ;
-- 5550 Bøger
UPDATE entries SET entry_class = 5550 WHERE entry_text LIKE 'GOOGLE%PLAY%' ;
UPDATE entries SET entry_class = 5550 WHERE entry_text LIKE 'Kindle%' ;
UPDATE entries SET entry_class = 5550 WHERE entry_text LIKE 'Bog & Idé%' ;
-- 5600 Tøj / sko
UPDATE entries SET entry_class = 5600 WHERE entry_text LIKE 'ONLY %' ;
UPDATE entries SET entry_class = 5600 WHERE entry_text LIKE 'Deichmann%' ;
UPDATE entries SET entry_class = 5600 WHERE entry_text LIKE 'H&M%' ;
UPDATE entries SET entry_class = 5600 WHERE entry_text LIKE 'COLOSSEUM%' ;
-- 5650 Boligudstyr
UPDATE entries SET entry_class = 5650 WHERE entry_text LIKE 'IKEA%HFB%' ;
UPDATE entries SET entry_class = 5650 WHERE entry_text LIKE 'HN %' ;
UPDATE entries SET entry_class = 5650 WHERE entry_text LIKE 'Silvan%' ;
-- 5700 Kæledyr
UPDATE entries SET entry_class = 5700 WHERE entry_text LIKE 'ZOOPLUS%' ;
UPDATE entries SET entry_class = 5700 WHERE entry_text LIKE 'Plantorama%' ;
UPDATE entries SET entry_class = 5700 WHERE entry_text LIKE 'Maxi Zoo%' ;
UPDATE entries SET entry_class = 5700 WHERE entry_text LIKE '%Dyrekl%' ;
-- 5750 Diverse
UPDATE entries SET entry_class = 5750 WHERE entry_text LIKE 'Flying Tiger %' ;
UPDATE entries SET entry_class = 5750 WHERE entry_text LIKE '%Nordea-min%' ;
UPDATE entries SET entry_class = 5750 WHERE entry_text LIKE 'Steam%' ;
UPDATE entries SET entry_class = 5750 WHERE entry_text LIKE 'Microsoft%' ;
UPDATE entries SET entry_class = 5750 WHERE entry_text LIKE 'AMZN%' ;
UPDATE entries SET entry_class = 5750 WHERE entry_text LIKE 'Biltema%' ;
UPDATE entries SET entry_class = 5750 WHERE entry_text LIKE 'jem&fix%' ;
UPDATE entries SET entry_class = 5750 WHERE entry_text LIKE 'Xsolla%' ;
UPDATE entries SET entry_class = 5750 WHERE entry_text LIKE '%Roblox%' ;
UPDATE entries SET entry_class = 5750 WHERE entry_text LIKE '%CHILDSPLAY%' ;
UPDATE entries SET entry_class = 5750 WHERE entry_text LIKE 'Kortafgift%' ;

UPDATE entries SET entry_class = 6000 WHERE entry_text LIKE 'Agoda%' ;
UPDATE entries SET entry_class = 6000 WHERE entry_text LIKE 'HOTELSCOM%' ;
UPDATE entries SET entry_class = 6000 WHERE entry_text LIKE 'SAS%' ;
UPDATE entries SET entry_class = 6000 WHERE entry_text LIKE 'Lalandia%' ;

UPDATE entries SET entry_class = 7000 WHERE entry_text LIKE 'Athia x5' ;
UPDATE entries SET entry_class = 7000 WHERE entry_text LIKE 'Athia Børneopsparing' ;
UPDATE entries SET entry_class = 7050 WHERE entry_text LIKE '%PROSA%' ;

-- SDU server
UPDATE entries SET entry_class = 0 WHERE (entry_text LIKE 'Overførsel%' OR entry_text LIKE 'Hetzner%') AND entry_amount > 250 AND entry_amount < 260;

-- Business expenses
UPDATE entries SET entry_class = 0 WHERE entry_id IN (SELECT entry_id FROM account_entries WHERE acc_id IN (2, 4, 5, 6)) ;
UPDATE entries SET entry_class = 0 WHERE entry_id IN (SELECT entry_id FROM view_entries WHERE view_id = 1) ;

-- Interest
UPDATE entries SET entry_class = 5750 WHERE entry_text LIKE 'Renter%' ;
