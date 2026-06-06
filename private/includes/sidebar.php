<!-- SIDEBAR -->
    <aside class="sidebar d-flex flex-column p-3">

        <div class="sidebar-logo text-center mb-4">
            <img src="/projeto_sibdas/assets/images/imagem_logo1-semfundo.png" alt="Logo TechMed Solutions"
                class="img-fluid">
        </div>

        <!-- UTILIZADOR -->

        <div class="dropdown utilizador-sidebar mx-3 mb-4">

            <button class="btn w-100 border-0 rounded-3 text-start dropdown-toggle utilizador-btn"
                data-bs-toggle="dropdown">

                <div class="d-flex align-items-center">

                    <div class="avatar-utilizador">

                        <i class="fa-solid fa-user"></i>

                    </div>

                    <div class="ms-2">

                        <div class="nome-utilizador">
                            Maria Silva
                        </div>

                        <div class="cargo-utilizador">
                            Administrador
                        </div>

                    </div>

                </div>

            </button>

            <ul class="dropdown-menu w-100">

                <li>
                    <h6 class="dropdown-header">
                        maria.silva@techmed.pt
                    </h6>
                </li>

                <li>
                    <hr class="dropdown-divider">
                </li>

                <li>
                    <a class="dropdown-item" href="#">

                        <i class="fa-solid fa-key me-2"></i>
                        Alterar Palavra-passe

                    </a>
                </li>

            </ul>

        </div>


        <nav class="sidebar-menu nav nav-pills flex-column gap-2">
            <a href="/projeto_sibdas/private/views/dashboard/dashboard.php" class="nav-link  <?= ($paginaAtiva == 'dashboard') ? 'active' : '' ?>">
                <i class="fa-solid fa-chart-line"></i>
                Dashboard
            </a>

            <a href="/projeto_sibdas/private/views/equipamentos/equipamentos.php" class="nav-link  <?= ($paginaAtiva == 'equipamentos') ? 'active' : '' ?>">
                <i class="fa-solid fa-screwdriver-wrench"></i>
                Equipamentos
            </a>

            <a href="/projeto_sibdas/private/views/localizacao/localizacao.php" class="nav-link  <?= ($paginaAtiva == 'localizacao') ? 'active' : '' ?>">
                <i class="fa-solid fa-location-dot"></i>
                Localizações
            </a>

            <a href="/projeto_sibdas/private/views/fornecedores/fornecedores.php" class="nav-link  <?= ($paginaAtiva == 'fornecedores') ? 'active' : '' ?>">
                <i class="fa-solid fa-truck-medical"></i>
                Fornecedores
            </a>

            <a href="/projeto_sibdas/private/views/gestao_conteudos/gestao_conteudos.php" class="nav-link  <?= ($paginaAtiva == 'gestao_conteudos') ? 'active' : '' ?>">
                <i class="fa-solid fa-pen-to-square"></i>
                Gestão de Conteúdos
            </a>
        </nav>

        <div class="sidebar-footer mt-auto">
            <a href="/projeto_sibdas/public/index.php" class="nav-link">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
                Sair
            </a>
        </div>

    </aside>