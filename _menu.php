	<!-- BEGIN SIDEBAR -->
	<div class="page-sidebar-wrapper">
		<!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
		<!-- DOC: Change data-auto-speed="200" to adjust the sub menu slide up/down speed -->
		<div class="page-sidebar navbar-collapse collapse">
			<!-- BEGIN SIDEBAR MENU -->
			<ul class="page-sidebar-menu" data-auto-scroll="true" data-slide-speed="200">
				<!-- DOC: To remove the sidebar toggler from the sidebar you just need to completely remove the below "sidebar-toggler-wrapper" LI element -->
				<li class="sidebar-toggler-wrapper">
					<!-- BEGIN SIDEBAR TOGGLER BUTTON -->
					<div class="sidebar-toggler">
					</div>
					<!-- END SIDEBAR TOGGLER BUTTON -->
				</li>
				<!-- DOC: To remove the search box from the sidebar you just need to completely remove the below "sidebar-search-wrapper" LI element -->
				<li class="sidebar-search-wrapper hidden-xs">
					<!-- BEGIN RESPONSIVE QUICK SEARCH FORM -->
					<!-- DOC: Apply "sidebar-search-bordered" class the below search form to have bordered search box -->
					<!-- DOC: Apply "sidebar-search-bordered sidebar-search-solid" class the below search form to have bordered & solid search box -->
					<form class="sidebar-search" action="extra_search.html" method="POST">
						<a href="javascript:;" class="remove">
						<i class="icon-close"></i>
						</a>
						<!--div class="input-group">
							<input type="text" class="form-control" placeholder="Search...">
							<span class="input-group-btn">
							<a href="javascript:;" class="btn submit"><i class="icon-magnifier"></i></a>
							</span>
						</div-->
					</form>
					<!-- END RESPONSIVE QUICK SEARCH FORM -->
				</li>
				<? if (fnVerifica_Grant('dashboard')) { ?>
				<li class="last <? if ($MENU_ATIVO == 'dashboard') echo 'active'; ?>">
					<a href="../dashboard/">
					<i class="icon-home"></i>
					<span class="title">Resumo</span>
					<span class="selected"></span>
					</a>
				</li>
				<? } ?>
				<? if (fnVerifica_Grant('administradores')) { ?>
				<li class="last <? if ($MENU_ATIVO == 'administradores') echo 'active'; ?>">
					<a href="../administradores/">
					<i class="icon-user"></i>
					<span class="title">Administradores</span>
					<span class="selected"></span>
					</a>
				</li>
				<? } ?>
				<? if (fnVerifica_Grant('auditoria')) { ?>
				<li class="last <? if ($MENU_ATIVO == 'auditoria') echo 'active'; ?>">
					<a href="../auditoria/">
					<i class="icon-briefcase"></i>
					<span class="title">Auditoria</span>
					<span class="selected"></span>
					</a>
				</li>
				<? } ?>
				<? if (fnVerifica_Grant('configuracoes')) { ?>
				<li class="last <? if ($MENU_ATIVO == 'configuracoes') echo 'active'; ?>">
					<a href="../configuracoes/">
					<i class="fa fa-cogs"></i>
					<span class="title">Configurações</span>
					<span class="selected"></span>
					</a>
				</li>
				<? } ?>
				<? if (fnVerifica_Grant('sanitizacaolocal')) { ?>
				<li class="last <? if ($MENU_ATIVO == 'sanitizacaolocal') echo 'active'; ?>">
					<a href="../sanitizacaolocal/">
					<i class="fa fa-code-fork"></i>
					<span class="title">Sanitização de local</span>
					<span class="selected"></span>
					</a>
				</li>
				<? } ?>
				<? if (fnVerifica_Grant('clientes')) { ?>
				<li class="last <? if ($MENU_ATIVO == 'clientes') echo 'active'; ?>">
					<a href="../clientes/">
					<i class="fa fa-users"></i>
					<span class="title">Clientes</span>
					<span class="selected"></span>
					</a>
				</li>
				<? } ?>
				<? if (fnVerifica_Grant('locais')) { ?>
				<li class="last <? if ($MENU_ATIVO == 'locais') echo 'active'; ?>">
					<a href="../locais/">
					<i class="fa fa-map-marker"></i>
					<span class="title">Locais</span>
					<span class="selected"></span>
					</a>
				</li>
				<? } ?>
				<? if (fnVerifica_Grant('clienteslocais')) { ?>
				<li class="last <? if ($MENU_ATIVO == 'clienteslocais') echo 'active'; ?>">
					<a href="../clienteslocais/">
					<i class="fa fa-link"></i>
					<span class="title">Clientes &#8596; Locais</span>
					<span class="selected"></span>
					</a>
				</li>
				<? } ?>
				<? if ((fnVerifica_Grant('gerarpromos')) || (fnVerifica_Grant('listapromos')) ){ ?>
				<li class="last <? if (in_array($MENU_ATIVO,array('gerarpromos','listarpromos'))) echo 'active'; ?>">
				<a href="javascript:;">
						<i class="fa fa-tags"></i>
						<span class="title">Promos</span>
						<span class="arrow"></span>
						<span class="selected"></span>
						</a>
						<ul class="sub-menu">
						<!-- BEGIN REPORT OPTION -->
							<? if (fnVerifica_Grant('gerarpromos')) { ?>
							<li class="<? if ($MENU_ATIVO == 'gerarpromos') echo 'active'; ?>">
								<a href="../promos/gerarpromos.php">
								Gerar promos</a>
							</li>
							<? } ?>
							<? if (fnVerifica_Grant('listarpromos')) { ?>
							<li class="<? if ($MENU_ATIVO == 'listarpromos') echo 'active'; ?>">
								<a href="../promos/listarpromos.php">
								Listar promos</a>
							</li>
							<? } ?>
						<!-- END REPORT OPTION -->
						</ul>
					</li>
				<? } ?>
				<? if ((fnVerifica_Grant('confirmacoes_diarias')) || (fnVerifica_Grant('consumo_sms')) || (fnVerifica_Grant('visao_confirmacoes')) || (fnVerifica_Grant('visao_alunos_confirmados')) || (fnVerifica_Grant('respostas_robo')) || (fnVerifica_Grant('historico_aluno')) || (fnVerifica_Grant('confirmacoes_e_senhas')) || (fnVerifica_Grant('logs_no_sistema'))) { ?>
				<li class="last <? if (in_array($MENU_ATIVO,array('confirmacoes_diarias','consumo_sms','mensagens_enviadas','visao_confirmacoes','visao_alunos_confirmados','respostas_robo','historico_aluno','confirmacoes_e_senhas','logs_no_sistema'))) echo 'active'; ?>">
					<a href="javascript:;">
					<i class="icon-bar-chart"></i>
					<span class="title">Relatórios</span>
					<span class="selected"></span>
					<span class="arrow"></span>
					</a>
					<ul class="sub-menu">
					<!-- BEGIN REPORT OPTION -->
						<? if (fnVerifica_Grant('logs_no_sistema')) { ?>
						<li class="<? if ($MENU_ATIVO == 'logs_no_sistema') echo 'active'; ?>">
							<a href="../relatorios/logs_no_sistema.php">
							Logs no Sistema</a>
						</li>
						<? } ?>
					<!-- END REPORT OPTION -->
					</ul>
				</li>
				<? } ?>
			</ul>
			<!-- END SIDEBAR MENU -->
		</div>
	</div>
	<!-- END SIDEBAR -->