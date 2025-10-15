-- Fix HTML entities in posts table
-- This will decode HTML entities that were incorrectly stored in the database

UPDATE posts 
SET title = 
    REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(
        title,
        '&aacute;', 'á'),
        '&eacute;', 'é'),
        '&iacute;', 'í'),
        '&oacute;', 'ó'),
        '&ouml;', 'ö'),
        '&odacute;', 'ő'),
        '&uacute;', 'ú'),
        '&uuml;', 'ü'),
        '&udacute;', 'ű'),
        '&Aacute;', 'Á'),
        '&Eacute;', 'É'),
        '&Iacute;', 'Í'),
        '&Oacute;', 'Ó'),
        '&Ouml;', 'Ö'),
        '&Odacute;', 'Ő'),
        '&Uacute;', 'Ú'),
        '&Uuml;', 'Ü'),
        '&Udacute;', 'Ű'),
        '&amp;', '&'),
        '&lt;', '<'),
        '&gt;', '>'),
        '&quot;', '"'),
        '&#039;', "'")
WHERE title LIKE '%&%';

UPDATE posts 
SET body = 
    REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(
        body,
        '&aacute;', 'á'),
        '&eacute;', 'é'),
        '&iacute;', 'í'),
        '&oacute;', 'ó'),
        '&ouml;', 'ö'),
        '&odacute;', 'ő'),
        '&uacute;', 'ú'),
        '&uuml;', 'ü'),
        '&udacute;', 'ű'),
        '&Aacute;', 'Á'),
        '&Eacute;', 'É'),
        '&Iacute;', 'Í'),
        '&Oacute;', 'Ó'),
        '&Ouml;', 'Ö'),
        '&Odacute;', 'Ő'),
        '&Uacute;', 'Ú'),
        '&Uuml;', 'Ü'),
        '&Udacute;', 'Ű'),
        '&amp;', '&'),
        '&lt;', '<'),
        '&gt;', '>'),
        '&quot;', '"'),
        '&#039;', "'")
WHERE body LIKE '%&%';
