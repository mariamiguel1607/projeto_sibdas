=====================================
TechMed Solutions — SIBDAS
Sistema de Inventário de Equipamentos Médicos
=====================================

Nome do projeto: TechMed Solutions
Nome do estudante: Maria Miguel Ferreira
Número do estudante: 1241375
Unidade curricular: Sistemas de Informação e Base de Dados Aplicados à Saúde
Curso: LEBIOM
Ano letivo: 2025/2026

=====================================
DESCRIÇÃO
=====================================

Aplicação web para gestão do inventário hospitalar de equipamentos médicos,
desenvolvida com PHP, MySQL, Bootstrap, jQuery e DataTables.

O sistema inclui duas componentes:
- Front Office: website institucional da empresa TechMed Solutions (área pública)
- Back Office: aplicação de gestão do inventário hospitalar (área privada)

=====================================
INSTALAÇÃO E EXECUÇÃO
=====================================

1. Requisitos:
   - Laragon com Apache e MySQL
   - PHP 8.x
   - MySQL 8.x
   - Browser moderno (Google Chrome, Firefox ou Edge)

2. Instalação:
   - Instalar o Laragon caso ainda não esteja instalado (https://laragon.org)
   - Copiar a pasta sibdas para: C:\laragon\www\
   - Resultado final: C:\laragon\www\sibdas\1241375\techmedsolutions\

3. Configuração da base de dados:
   - Servidor: vsgate-s1.dei.isep.ipp.pt:10464
   - Base de dados: db1241375
   - As credenciais de acesso à BD estão definidas em config/config.php
   - Importar os ficheiros SQL incluídos no ZIP:
       a) CreateTables1241375.sql — cria a estrutura das tabelas
       b) Inserts1241375.sql — insere os dados de teste
   - Pode ser feito via HeidiSQL, phpMyAdmin ou linha de comandos MySQL

4. Execução da aplicação:
   - Abrir o Laragon
   - Clicar em "Iniciar tudo" (Start All) para arrancar o Apache e o MySQL
   - Confirmar que o Apache e o MySQL aparecem a verde no Laragon
   - Abrir o browser e aceder a:

       Área pública:
       http://127.0.0.1/sibdas/1241375/techmedsolutions/public/index.php

       Área privada (login):
       http://127.0.0.1/sibdas/1241375/techmedsolutions/private/login/login.php

5. Notas importantes:
   - O Laragon tem de estar em execução para a aplicação funcionar
   - O ficheiro config/config.php contém as credenciais da BD e as chaves
     de encriptação — não deve ser partilhado em ambientes de produção
   - O BASE_URL está definido como: /sibdas/1241375/techmedsolutions
   - Os ficheiros PDF carregados são guardados em: assets/uploads/documentos/

=====================================
CREDENCIAIS DE ACESSO
=====================================

Perfil: Administrador
   Email: miguel.ferreira@techmedsolutions.pt
   Password: Admin@2025
    Acesso: Todas as funcionalidades — equipamentos, fornecedores, localizações,
           dashboard, gestão de conteúdos da área pública, mensagens de contacto
           e histórico de atividade.

Perfil: Técnico
   Email: joao.silva@techmedsolutions.pt
   Password: Tecnico@2024
    Acesso: Equipamentos, fornecedores e localizações (inserir, editar, desativar, eliminar);
           dashboard e histórico de atividade. Sem acesso à gestão de conteúdos
           e mensagens.

Perfil: Profissional de Saúde
   Email: ana.costa@techmedsolutions.pt
   Password: Saude@2024
   Acesso: Consulta de equipamentos, fornecedores, localizações e dashboard.
           Sem permissão para inserir, editar, eliminar ou reativar registos.

=====================================
TESTES PRINCIPAIS
=====================================

1. AUTENTICAÇÃO
   - Testar login com cada um dos 3 perfis acima
   - Testar login com credenciais erradas (deve mostrar erro)
   - Testar logout (deve destruir a sessão)
   - Testar acesso direto a páginas privadas sem login (deve redirecionar)

2. EQUIPAMENTOS (CRUD completo)
   - Inserir novo equipamento com validação (formulário multi-step com 8 passos)
   - Listar equipamentos com filtros (pesquisa, categoria, estado, criticidade, serviço, fornecedor)
   - Consultar ficha detalhada de equipamento (tabs com dados gerais, aquisição, acessórios, localização, fornecedor, garantias, documentação, observações)
   - Editar equipamento existente com validação
   - Desativar/reativar equipamento (soft delete)
   - Exportar ficha do equipamento em CSV, JSON e PDF

3. FORNECEDORES (CRUD completo)
   - Inserir fornecedor com validação (NIF, telefone português, email, URL, etc)
   - Listar com pesquisa e filtro por tipo
   - Editar fornecedor com validação
   - Desativar fornecedor ( com reatribuição de equipamentos associados)
   - Reativar fornecedor 

4. LOCALIZAÇÕES (CRUD completo)
   - Inserir localização com validação (Edifício, piso, departamento, etc)
   - Editar localização com validação
   - Desativar localização (com reatribuição de equipamentos associados)
   - Reativar localização

5. DASHBOARD
   - Verificar indicadores: total, ativos, em manutenção, inativos
   - Verificar equipamentos por categoria, por serviço
   - Verificar garantias expiradas e a expirar nos próximos 4 meses
   - Verificar equipamentos de suporte de vida por serviço

6. GESTÃO DE CONTEÚDOS (apenas Administrador)
   - Editar textos da página pública (serviços, sobre nós, FAQ, contactos, rodapé)
   - Verificar que as alterações refletem na área pública

7. MENSAGENS DE CONTACTO
   - Enviar mensagem pelo formulário público
   - Verificar notificação (badge) na sidebar do Administrador
   - Marcar mensagem como lida

8. HISTÓRICO DE ATIVIDADE
   - Realizar uma operação num equipamento (inserir, editar, desativar ou reativar)
   - Clicar em "Histórico" na sidebar (disponível para Administrador e Técnico)
   - Verificar que a ação fica registada com data, hora e utilizador

9. SEGURANÇA
   - Passwords armazenadas com password_hash/password_verify
   - Prepared statements em todas as queries com filtros
   - IDs encriptados com AES-256-CBC nos URLs
   - Emails encriptados com AES na base de dados
   - Registo de tentativas de login na tabela log_acessos
   - Controlo de acesso por perfil (sidebar e server-side)

=============================================
FUNCIONALIDADES DE VALORIZAÇÃO IMPLEMENTADAS
=============================================

- Upload real de ficheiros PDF com nomes únicos (uniqid)
- Exportação de dados em CSV, JSON e PDF
- Histórico de atividade dos equipamentos (offcanvas)
- Mensagens de contacto com notificação e marcação como lida
- Registo de eventos de autenticação (log_acessos)
- Alteração de palavra-passe com validação
- Layout responsivo com sidebar hamburger para mobile/tablet
- Classificação de criticidade com destaque visual (badges)
- Toast notifications nas exportações
- Popover informativo nos estados dos equipamentos

=====================================
TECNOLOGIAS UTILIZADAS
=====================================

Linguagens e frameworks:
- PHP 8.x (backend)
- HTML5 + CSS3 (estrutura e estilos)
- JavaScript (interatividade)
- MySQL 8.x (base de dados relacional)

Bibliotecas e componentes frontend:
- Bootstrap 5 (layout responsivo e componentes UI)
- jQuery 3.6 (manipulação do DOM e interatividade)
- DataTables (tabelas interativas com paginação e pesquisa)
- Flatpickr (seletor de datas)
- Font Awesome (ícones vetoriais)

Segurança e acesso a dados:
- PDO com prepared statements (acesso seguro à base de dados)
- OpenSSL AES-256-CBC (encriptação de IDs nos URLs)
- AES_ENCRYPT/AES_DECRYPT MySQL (encriptação de emails na BD)
- password_hash / password_verify PHP (armazenamento seguro de passwords)

=====================================
NOTAS ADICIONAIS
=====================================

- O ficheiro config.php contém credenciais da BD e chaves de encriptação.
  Num ambiente de produção, este ficheiro não deveria estar no repositório.
- A coluna email da tabela utilizadores é do tipo BLOB por usar AES_ENCRYPT ao nível do MySQL.
- O sistema utiliza soft delete (campo ativo) em equipamentos, fornecedores e localizações.
- Se alterar a password de um utilizador pelo modal, a password original deixa de funcionar.
  As passwords atuais e originais são as indicadas nas credenciais acima.