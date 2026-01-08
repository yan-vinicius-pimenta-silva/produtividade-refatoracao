<?php
$classes = array();
$apelidos = array();

if (@$menu) {
	foreach ($menu as $value) {
		$classes[] = $value->classe;
		$apelidos[] = $value->apelido;
	}
}
?>

<style>
	.image>img {
		width: 48px !important;
		height: 48px !important;
	}
</style>
<!-- #Top Bar -->
<section>
	<!-- Left Sidebar -->
	<aside id="leftsidebar" class="sidebar">
		<!-- User Info -->
		<div class="user-info">
			<div class="image">
				<?php
				if ($this->session->userdata['logged_in']['logo_img'] != null)
					echo '<img src="' . base_url() . 'uploads/empresa_' . $this->session->userdata["logged_in"]["id_empresa"] . '/logo/' . $this->session->userdata["logged_in"]["logo_img"] . '" width="48" height="48" alt="User" />';
				else
					echo '<img src=' . $this->session->userdata["logged_in"]["foto"] . ' "alt="User" />';
				?>
			</div>

			<div class="info-container">
				<div class="name" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?= $this->session->userdata['logged_in']['nome'] ?></div>
				<div class="email"><?= $this->session->userdata['logged_in']['usuario'] ?></div>
				<a href="<?= base_url() ?>login/logout" class="btn-group user-helper-dropdown">
					<i class="material-icons">input</i>
				</a>
			</div>
		</div>
		<!-- #User Info -->
		<!-- Menu -->
		<div class="menu">
			<ul class="list">
				<li class="header">
					MENU NAVEGAÇÃO
				</li>
				<li <?php if ($this->uri->uri_string() == "home") {
						echo 'class="active"';
					} ?>>
					<a href="<?= base_url() ?>home">
						<i class="material-icons">home</i>
						<span>
							Home
						</span>
					</a>
				</li>
				<?php
				$array2 = array(
					'parametro',
					'unidadefiscal'
				);

				$ativo = "";
				if (in_array(strstr($this->uri->uri_string(), "/", true), $array2) || in_array($this->uri->uri_string(), $array2)) {
					$ativo = "class='active'";
				}
				?>


				<!-- Adicionado -->
				<?php if (in_array($this->session->userdata['logged_in']['nivel'], [CHEFE, SECRETARIO])) { ?>
					<li <?= (strpos($this->uri->uri_string(), "deducao") !== false) ? 'class="active"' : '' ?>>
						<a href="javascript:void(0);" class="menu-toggle">
							<i class="material-icons">group_remove</i>
							<span>
								Deduções
							</span>
						</a>
						<ul class="ml-menu">
							<li <?= ($this->uri->uri_string() == "deducao/cadastro") ? 'class="active"' : '' ?>>
								<?php if (in_array($this->session->userdata['logged_in']['nivel'], [CHEFE, SECRETARIO])) { ?>
									<a href="<?= base_url('deducao/cadastro') ?>">
										<span>
											Cadastrar
										</span>
									</a>
								<?php } ?>
							</li>
							<li <?= ($this->uri->uri_string() == "deducao") ? 'class="active"' : '' ?>>
								<a href="<?= base_url('deducao/') ?>">
									<span>
										Consultar
									</span>
								</a>
							</li>
						</ul>
					</li>
				<?php } ?>
				<!-- ** -->

				<!-- Adicionado -->
				<?php if ($this->session->userdata['logged_in']['parametros_empresa']->os == 1) { ?>
					<a href="javascript:void(0);" class="menu-toggle">
						<i class="material-icons">description</i>
						<span>
							Ordens de serviço
						</span>
					</a>
					<ul class="ml-menu">
						<li>
							<?php if (in_array($this->session->userdata['logged_in']['nivel'], [CHEFE, SECRETARIO])) { ?>
								<a href="<?= base_url() ?>gerarordem/">
									<span>
										Gerar
									</span>
								</a>
							<?php } ?>
							<a href="<?= base_url() ?>consultarordem/">
								<span>
									Consultar
								</span>
							</a>
						</li>
					</ul>
				<?php } ?>
				<!-- ** -->
				<?php if (in_array($this->session->userdata['logged_in']['nivel'], [CHEFE, SECRETARIO, FISCAL])) : ?>
					<!-- Adicionado -->
					<li <?= ($this->uri->uri_string() == "home/index_antigo") ? 'class="active"' : '' ?>>
						<a href="<?= base_url('home/index_antigo') ?>">
							<i class="material-icons">history</i>
							<span>
								Atividades Lei anterior
							</span>
						</a>
					</li>
				<?php endif; ?>

				<!-- ** -->

				<?php if ($this->session->userdata['logged_in']['nivel'] == CHEFE) { ?>
					<li <?= $ativo ?>>
						<a href="javascript:void(0);" class="menu-toggle">
							<i class="material-icons">edit</i>
							<span>
								Parâmetros
							</span>
						</a>
						<ul class="ml-menu">
							<li>
								<a href="<?= base_url('parametro/') ?>">
									<span>
										Atividades
									</span>
								</a>
							</li>
							<li>
								<a href="<?= base_url('unidadefiscal/') ?>">
									<span>
										Unidade Fiscal (UFESP)
									</span>
								</a>
							</li>
						</ul>

					</li>
					<li <?= ($this->uri->uri_string() == "usuario") ? 'class="active"' : '' ?>>
						<a href="<?= base_url() ?>usuario">
							<i class="material-icons">person</i>
							<span>
								Usuários
							</span>
						</a>
					</li>
				<?php } ?>
				<li <?= ($this->uri->uri_string() == "atividade/excluidas") ? "class=\"active\"" : '' ?>>
					<a href="<?= base_url('lixeira') ?>">
						<i class="material-icons">delete</i>
						<span>
							Lixeira
						</span>
					</a>
				</li>
				<?php if ($this->session->userdata['logged_in']['id_empresa'] == 6) { ?>
					<li <?= ($this->uri->uri_string() == "admin") ? "class=\"active\"" : '' ?>>
						<a href="javascript:void(0);" class="menu-toggle">
							<i class="material-icons">edit</i>
							<span>
								Administração
							</span>
						</a>
						<ul class="ml-menu">
							<li>
								<a href="<?= base_url() ?>admin/">
									<span>
										Administração
									</span>
								</a>
							</li>
						</ul>
					</li>
				<?php } ?>
			</ul>
		</div>
		<!-- #Menu -->
		<!-- Footer -->
		<div class="legal">
			<div class="copyright">
				&copy; <?= date('Y') ?>
				<a href="javascript:void(0);">
					Divisão de sistemas / PMA
				</a>.
			</div>
			<div class="version">
				<b>Versão: </b> 2.0
			</div>
		</div>
		<!-- #Footer -->
	</aside>
	<!-- #END# Left Sidebar -->

</section>