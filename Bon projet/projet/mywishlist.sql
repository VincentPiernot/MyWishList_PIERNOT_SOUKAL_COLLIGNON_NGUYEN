SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `messagePublic`;
DROP TABLE IF EXISTS `participation`;
DROP TABLE IF EXISTS `item`;
DROP TABLE IF EXISTS `liste`;
DROP TABLE IF EXISTS `user`;

CREATE TABLE `user` (
   `idu` int(11) NOT NULL AUTO_INCREMENT,
    `username` varchar(16),
    `hash` varchar(256),
    PRIMARY KEY(idU)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `user` (`idu`,`username`,`hash`) VALUES (1,'admin','$2y$12$6Rp6.Sm9//IUlMA4xau7huUumEKfQVeoZxwKPOTK2AoVZ5At20zGW');

CREATE TABLE `liste` (
  `no` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `titre` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `expiration` date DEFAULT NULL,
  `tokenModif` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tokenAcces` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `publique` int(1) NOT NULL,
  PRIMARY KEY (`no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `liste` (`no`, `user_id`, `titre`, `description`, `expiration`, `tokenAcces` , `tokenModif`) VALUES
(1,	1,	'Pour feter le bac !',	'Pour un week-end a Nancy qui nous fera oublier les epreuves. ',	'2020-06-27',	'a6cd146b50e5f73', '6fbb9a350634fc85'),
(2,	1,	'Liste de mariage d\'Alice et Bob',	'Nous souhaitons passer un week-end royal a Nancy pour notre lune de miel :)',	'2020-06-30',	'199e8013a5845dc1',	'dff708988a8ab10b'),
(3,	1,	'C\'est l\'anniversaire de Charlie',	'Pour lui preparer une fete dont il se souviendra :)',	'2020-12-12',	'd134e3aa736556e6',	'9aa037b29e750050');

CREATE TABLE `item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `liste_id` int(11) NOT NULL,
  `nom` text NOT NULL,
  `descr` text,
  `img` text,
  `url` text,
  `tarif` decimal(5,2) DEFAULT NULL,
  `cagnotte` int(11) DEFAULT -1,
  FOREIGN KEY (`liste_id`) REFERENCES liste(no),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `Participation` (
    `item_id` int(11) NOT NULL,
    `montant` int(11) NOT NULL,
    `nomP` varchar(25) NOT NULL,
    `message` varchar(300) DEFAULT NULL,
    `user_id` int(11) DEFAULT NULL,
    FOREIGN KEY (`item_id`) REFERENCES item(id),
    FOREIGN KEY (`user_id`) REFERENCES user(idu),
    PRIMARY KEY(`item_id`,`nomP`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `item` (`id`, `liste_id`, `nom`, `descr`, `img`, `url`, `tarif`) VALUES
(1,	2,	'Champagne',	'Bouteille de champagne + flutes + jeux a gratter',	'web/img/champagne.jpg',	'',	20.00),
(2,	2,	'Musique',	'Partitions de piano a 4 mains',	'web/img/musique.jpg',	'',	25.00),
(3,	2,	'Exposition',	'Visite guidee de l exposition REGARDER a la galerie Poirel',	'web/img/poirelregarder.jpg',	'',	14.00),
(4,	3,	'Gouter',	'Gouter au FIFNL',	'web/img/gouter.jpg',	'',	20.00 ),
(5,	3,	'Projection',	'Projection courts-metrages au FIFNL',	'web/img/film.jpg',	'',	10.00),
(8,	3,	'Origami',	'Baguettes magiques en Origami en buvant un the',	'web/img/origami.jpg',	'',	12.00 ),
(9,	3,	'Livres',	'Livre bricolage avec petits-enfants + Roman',	'web/img/bricolage.jpg',	'',	24.00 ),
(10,	2,	'Diner  Grand Rue ',	'Diner au Grand Ru(e) (Aperitif / Entree / Plat / Vin / Dessert / Cafe)',	'web/img/grandrue.jpg',	'',	59.00 ),
(11,	1,	'Visite guidee',	'Visite guidee personnalisee de Saint-Epvre jusqu a Stanislas',	'web/img/place.jpg',	'',	11.00 ),
(12,	2,	'Bijoux',	'Bijoux de manteau + Sous-verre pochette de disque + Lait apres-soleil',	'web/img/bijoux.jpg',	'',	29.00 ),
(26,	1,	'Planetes Laser',	'Laser game : Gilet electronique et pistolet laser comme materiel, vous voila equipe.',	'web/img/laser.jpg',	'',	15.00 ),
(27,	1,	'Fort Aventure',	'Decouvrez Fort Aventure a Bainville-sur-Madon, un site Accropierre unique en Lorraine ! Des Parcours Acrobatiques pour petits et grands, Jeu Mission Aventure, Crypte de Crapahute, Tyrolienne, Saut a l\'elastique inverse, Toboggan geant... et bien plus encore.',	'web/img/fort.jpg',	'',	25.00 );

CREATE TABLE `messagePublic` (
  `idM` int(11) NOT NULL AUTO_INCREMENT,
  `liste_id` int(11) NOT NULL,
  `message` varchar(300) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  FOREIGN KEY (`liste_id`) REFERENCES liste(no),
  PRIMARY KEY (`idM`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
