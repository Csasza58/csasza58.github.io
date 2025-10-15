-- Kiemelt poszt rendszer frissítése: 3 kiemelt poszt támogatása
-- is_featured mező: 0 = nem kiemelt, 1 = kiemelt #1, 2 = kiemelt #2, 3 = kiemelt #3

-- Először hozzáadunk egy featured_date oszlopot a kiemelt dátum nyilvántartásához
ALTER TABLE posts ADD COLUMN featured_date TIMESTAMP NULL DEFAULT NULL AFTER is_featured;

-- Frissítjük a jelenleg kiemelt poszt(ok) featured_date értékét
UPDATE posts SET featured_date = NOW() WHERE is_featured = 1;

-- Ellenőrizzük az eredményt
-- SELECT id, title, is_featured, featured_date FROM posts ORDER BY featured_date DESC;