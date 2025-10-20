#!/bin/bash

echo "🚀 Iniciando ambiente de desenvolvimento E-commerce"
echo "=================================================="

# Verificar se Docker está instalado
if ! command -v docker &> /dev/null; then
    echo "❌ Docker não está instalado. Por favor, instale o Docker primeiro."
    echo "   Visite: https://docs.docker.com/get-docker/"
    exit 1
fi

# Verificar se Docker Compose está disponível
if ! docker compose version &> /dev/null; then
    echo "❌ Docker Compose não está disponível. Por favor, instale o Docker Compose primeiro."
    exit 1
fi

echo "✅ Docker e Docker Compose encontrados"

# Parar containers existentes se houver
echo "🛑 Parando containers existentes..."
docker compose down

# Iniciar os serviços
echo "🚀 Iniciando MySQL e phpMyAdmin..."
docker compose up -d

# Aguardar MySQL ficar pronto
echo "⏳ Aguardando MySQL ficar pronto..."
timeout=60
while ! docker exec ecommerce_mysql mysqladmin ping -h localhost --silent 2>/dev/null; do
    sleep 2
    timeout=$((timeout - 2))
    if [ $timeout -le 0 ]; then
        echo "❌ Timeout aguardando MySQL"
        exit 1
    fi
done

echo "✅ MySQL está rodando!"

# Verificar se o schema foi importado
echo "📊 Verificando se o banco foi criado..."
docker exec ecommerce_mysql mysql -u root -proot123 -e "USE project_db; SHOW TABLES;" 2>/dev/null

if [ $? -eq 0 ]; then
    echo "✅ Banco de dados criado com sucesso!"
else
    echo "⚠️  Importando schema manualmente..."
    docker exec -i ecommerce_mysql mysql -u root -proot123 project_db < first_task/database_schema.sql
fi

echo ""
echo "🎉 Ambiente configurado com sucesso!"
echo "=================================================="
echo "📊 MySQL:      localhost:3306"
echo "   Database:   project_db"
echo "   User:       root"
echo "   Password:   root123"
echo ""
echo "🌐 phpMyAdmin: http://localhost:8080"
echo "   User:       root"
echo "   Password:   root123"
echo ""
echo "🧪 Para testar a conexão, execute:"
echo "   cd first_task && php -S localhost:8000"
echo "   Acesse: http://localhost:8000/test_database.php"
echo ""
echo "🛑 Para parar os serviços:"
echo "   docker compose down"