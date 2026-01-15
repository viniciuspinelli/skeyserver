const mysql = require('mysql2');
const conn = mysql.createConnection({
    host: 'localhost', 
    user: 'root', 
    password: '', 
    database: 'weaponpaints', 
    port: 3306
});

const tables = [
    `CREATE TABLE IF NOT EXISTS wp_player_skins (
        steamid VARCHAR(18) NOT NULL,
        weapon_team INT NOT NULL DEFAULT 0,
        weapon_defindex INT NOT NULL,
        weapon_paint_id INT NOT NULL DEFAULT 0,
        weapon_wear FLOAT NOT NULL DEFAULT 0.0001,
        weapon_seed INT NOT NULL DEFAULT 0,
        weapon_nametag VARCHAR(128) DEFAULT NULL,
        weapon_stattrak INT NOT NULL DEFAULT -1,
        weapon_stattrak_count INT NOT NULL DEFAULT 0,
        weapon_sticker_0 VARCHAR(128) NOT NULL DEFAULT '0;0;0;0;0;0;0',
        weapon_sticker_1 VARCHAR(128) NOT NULL DEFAULT '0;0;0;0;0;0;0',
        weapon_sticker_2 VARCHAR(128) NOT NULL DEFAULT '0;0;0;0;0;0;0',
        weapon_sticker_3 VARCHAR(128) NOT NULL DEFAULT '0;0;0;0;0;0;0',
        weapon_sticker_4 VARCHAR(128) NOT NULL DEFAULT '0;0;0;0;0;0;0',
        weapon_keychain VARCHAR(128) NOT NULL DEFAULT '0;0;0;0;0',
        PRIMARY KEY (steamid, weapon_team, weapon_defindex)
    )`,
    `CREATE TABLE IF NOT EXISTS wp_player_knife (
        steamid VARCHAR(18) NOT NULL,
        weapon_team INT NOT NULL DEFAULT 0,
        knife VARCHAR(64) NOT NULL DEFAULT 'weapon_knife',
        PRIMARY KEY (steamid, weapon_team)
    )`,
    `CREATE TABLE IF NOT EXISTS wp_player_gloves (
        steamid VARCHAR(18) NOT NULL,
        weapon_team INT NOT NULL DEFAULT 0,
        weapon_defindex INT NOT NULL,
        PRIMARY KEY (steamid, weapon_team)
    )`,
    `CREATE TABLE IF NOT EXISTS wp_player_agents (
        steamid VARCHAR(18) NOT NULL PRIMARY KEY,
        agent_ct VARCHAR(64) DEFAULT NULL,
        agent_t VARCHAR(64) DEFAULT NULL
    )`,
    `CREATE TABLE IF NOT EXISTS wp_player_music (
        steamid VARCHAR(18) NOT NULL PRIMARY KEY,
        music_id INT NOT NULL DEFAULT 0
    )`,
    `CREATE TABLE IF NOT EXISTS wp_player_pins (
        steamid VARCHAR(18) NOT NULL PRIMARY KEY,
        id INT NOT NULL DEFAULT 0
    )`
];

let completed = 0;
tables.forEach((sql, i) => {
    conn.query(sql, (err) => {
        if(err) console.log('ERRO tabela', i+1, ':', err.message);
        else console.log('Tabela', i+1, 'criada com sucesso!');
        completed++;
        if(completed === tables.length) { 
            console.log('\n✅ Todas as tabelas criadas!'); 
            conn.end(); 
        }
    });
});
