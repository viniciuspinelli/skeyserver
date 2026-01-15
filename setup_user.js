const mysql = require('mysql2');
const conn = mysql.createConnection({
    host: 'localhost', 
    user: 'root', 
    password: '', 
    port: 3306
});

// Criar usuário que aceite conexões de qualquer IP
conn.query("CREATE USER IF NOT EXISTS 'wpuser'@'%' IDENTIFIED BY 'wp123456'", (err) => {
    if(err && !err.message.includes('exists')) {
        console.log('Erro ao criar usuario:', err.message);
    } else {
        console.log('✅ Usuario wpuser@% criado!');
    }
    
    // Conceder permissões
    conn.query("GRANT ALL PRIVILEGES ON weaponpaints.* TO 'wpuser'@'%'", (err2) => {
        if(err2) {
            console.log('Erro ao conceder permissoes:', err2.message);
        } else {
            console.log('✅ Permissoes concedidas!');
        }
        
        conn.query('FLUSH PRIVILEGES', () => {
            console.log('✅ Flush privileges OK!');
            console.log('\n=== CONFIGURACAO COMPLETA ===');
            console.log('Usuario: wpuser');
            console.log('Senha: wp123456');
            console.log('Banco: weaponpaints');
            console.log('Porta: 3306');
            conn.end();
        });
    });
});
