-- Módosítjuk a posts táblát a fejlettebb kiemelt poszt rendszerhez

-- Hozzáadjuk a manual_featured_date mezőt ami tárolja mikor lett manuálisan kiemeltté téve
ALTER TABLE posts ADD COLUMN manual_featured_date TIMESTAMP NULL DEFAULT NULL;

-- Frissítjük a meglévő kiemelt posztokat - akiknek is_featured = 1, azoknak beállítjuk a jelenlegi dátumot
UPDATE posts SET manual_featured_date = NOW() WHERE is_featured = 1;