<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Mapa de tudo que é editável no painel (Conteúdo do site).
 *
 * Cada campo tem: key (única), label, type, default e, opcionalmente, help.
 * Os "default" são os textos/imagens atuais do site: um campo nunca editado
 * mostra exatamente isso. Para acrescentar um campo novo, basta registrá-lo
 * aqui e usá-lo na view com os helpers ct(), ct_rich(), content() etc.
 *
 * Tipos:
 *  - text        linha única
 *  - rich        linha única que aceita *destaque* (cor dourada) e **negrito**
 *  - textarea    texto curto com quebras de linha
 *  - paragraphs  parágrafos (linha em branco separa), aceita **negrito** e [link](url)
 *  - markup      texto longo: "## Título", "- item de lista", parágrafos, **negrito**, [link](url)
 *  - image       imagem (upload)
 *  - list        lista repetível; "item" descreve os subcampos
 *  - icon/select escolha entre opções; bool = sim/não (usados dentro de listas)
 */
final class ContentRegistry
{
    /** Ícones disponíveis para escolha no painel. */
    public const ICONS = [
        'perfil'         => 'Pessoa',
        'assessoria'     => 'Pessoas / equipe',
        'predio'         => 'Prédio',
        'credito'        => 'Casa com cifrão',
        'analise'        => 'Gráfico',
        'juridico'       => 'Balança',
        'fgts'           => 'Carteira',
        'repasse'        => 'Documentos',
        'acompanhamento' => 'Rota / jornada',
        'bancos'         => 'Banco',
        'consorcio'      => 'Moedas',
        'cuidado'        => 'Coração',
        'simplicidade'   => 'Blocos conectados',
        'resultados'     => 'Seta de crescimento',
        'transparencia'  => 'Olho',
        'agilidade'      => 'Raio',
        'alvo'           => 'Alvo',
        'visao'          => 'Brilho',
        'check'          => 'Check',
    ];

    /** Perfis de parceiro aceitos pelo formulário (chave fixa, rótulo editável). */
    public const PARTNER_PROFILES = [
        ''            => 'Nenhum (só leva ao formulário)',
        'corretor'    => 'Corretor',
        'imobiliaria' => 'Imobiliária',
        'construtora' => 'Construtora / incorporadora',
    ];

    /** @var array<string,array<string,mixed>>|null */
    private static ?array $flat = null;

    /**
     * @return array<string, array{title:string, description:string, url:string, sections:array<string,array{title:string, help?:string, fields:array<int,array<string,mixed>>}>}>
     */
    public static function pages(): array
    {
        return [
            'home'        => self::homePage(),
            'layout'      => self::layoutPage(),
            'privacidade' => self::privacyPage(),
            'termos'      => self::termsPage(),
            'erros'       => self::errorsPage(),
            'seo'         => self::seoPage(),
        ];
    }

    /** @return array<string,mixed>|null */
    public static function field(string $key): ?array
    {
        if (self::$flat === null) {
            self::$flat = [];
            foreach (self::pages() as $page) {
                foreach ($page['sections'] as $section) {
                    foreach ($section['fields'] as $f) {
                        self::$flat[$f['key']] = $f;
                    }
                }
            }
        }
        return self::$flat[$key] ?? null;
    }

    public static function defaultValue(string $key): mixed
    {
        return self::field($key)['default'] ?? null;
    }

    // --- Helpers de definição --------------------------------------------

    /** @param array<string,mixed> $extra @return array<string,mixed> */
    private static function f(string $key, string $label, string $type, mixed $default, array $extra = []): array
    {
        return ['key' => $key, 'label' => $label, 'type' => $type, 'default' => $default] + $extra;
    }

    /** Cabeçalho padrão de seção (eyebrow, título, texto de apoio). @return array<int,array<string,mixed>> */
    private static function head(string $prefix, string $eyebrow, string $title, ?string $lead = null): array
    {
        $fields = [
            self::f("$prefix.eyebrow", 'Chamada pequena (acima do título)', 'text', $eyebrow),
            self::f("$prefix.title", 'Título', 'rich', $title, ['help' => 'Use *asteriscos* para destacar palavras em dourado.']),
        ];
        if ($lead !== null) {
            $fields[] = self::f("$prefix.lead", 'Texto de apoio', 'textarea', $lead);
        }
        return $fields;
    }

    // --- Página inicial ----------------------------------------------------

    /** @return array<string,mixed> */
    private static function homePage(): array
    {
        return [
            'title' => 'Página inicial',
            'description' => 'Todas as seções da home, na ordem em que aparecem no site.',
            'url' => '',
            'sections' => [
                'hero' => [
                    'title' => 'Topo (foto principal)',
                    'fields' => [
                        self::f('home.hero.image', 'Foto principal (computador)', 'image', 'img/hero.jpg', ['help' => 'Horizontal, de preferência 2400 × 1600 px, até 8 MB.']),
                        self::f('home.hero.image_mobile', 'Foto principal (celular)', 'image', 'img/hero-mobile.jpg', ['help' => 'Vertical, de preferência 1080 × 1600 px.']),
                        self::f('home.hero.eyebrow', 'Chamada pequena', 'text', 'Assessoria especializada em crédito imobiliário'),
                        self::f('home.hero.title', 'Título', 'rich', 'Experiência que conecta pessoas, *negócios e conquistas.*', ['help' => 'O trecho entre *asteriscos* aparece em dourado.']),
                        self::f('home.hero.text', 'Texto', 'textarea', 'Há mais de 30 anos, a DMR oferece orientação especializada para quem deseja financiar um imóvel e para profissionais que movimentam o mercado imobiliário.'),
                        self::f('home.hero.button_primary', 'Botão principal', 'text', 'Escolha seu caminho'),
                        self::f('home.hero.button_whatsapp', 'Botão do WhatsApp', 'text', 'Falar no WhatsApp'),
                        self::f('home.hero.whatsapp_message', 'Mensagem que já vem escrita no WhatsApp', 'text', 'Olá! Gostaria de falar com a DMR sobre crédito imobiliário.'),
                    ],
                ],
                'stats' => [
                    'title' => 'Números',
                    'fields' => [
                        self::f('home.stats.items', 'Números', 'list', [
                            ['value' => '30+', 'label' => 'anos de experiência'],
                            ['value' => '100 mil+', 'label' => 'unidades financiadas'],
                            ['value' => '4', 'label' => 'grandes bancos parceiros'],
                            ['value' => '100%', 'label' => 'do processo acompanhado'],
                        ], [
                            'item' => [
                                self::f('value', 'Número', 'text', ''),
                                self::f('label', 'Descrição', 'text', ''),
                            ],
                            'add_label' => 'Adicionar número', 'max' => 4,
                            'help' => 'Até 4 números. O layout foi feito para 4.',
                        ]),
                    ],
                ],
                'channels' => [
                    'title' => 'Escolha do caminho (Para você / Para parceiros)',
                    'fields' => [
                        self::f('home.channels.b2c_eyebrow', 'Card 1 — chamada', 'text', 'Para você'),
                        self::f('home.channels.b2c_title', 'Card 1 — título', 'text', 'Quero financiar um imóvel'),
                        self::f('home.channels.b2c_text', 'Card 1 — texto', 'textarea', 'Entenda suas opções e avance com orientação e acompanhamento especializados.'),
                        self::f('home.channels.b2c_button', 'Card 1 — botão', 'text', 'Conhecer soluções para você'),
                        self::f('home.channels.b2b_eyebrow', 'Card 2 — chamada', 'text', 'Para parceiros'),
                        self::f('home.channels.b2b_title', 'Card 2 — título', 'text', 'Sou parceiro imobiliário'),
                        self::f('home.channels.b2b_text', 'Card 2 — texto', 'textarea', 'Conte com suporte especializado para apoiar seus clientes e suas operações.'),
                        self::f('home.channels.b2b_button', 'Card 2 — botão', 'text', 'Conhecer soluções para parceiros'),
                    ],
                ],
                'b2c' => [
                    'title' => 'Para você (compradores)',
                    'fields' => array_merge(
                        self::head('home.b2c', 'Para você · Compradores', 'O caminho até o seu imóvel começa com uma boa orientação.', 'A DMR ajuda você a compreender as alternativas de financiamento, organizar a documentação e acompanhar cada etapa do processo com mais clareza.'),
                        [
                            self::f('home.b2c.button', 'Botão', 'text', 'Quero analisar meu financiamento'),
                            self::f('home.b2c.panel_title', 'Título do quadro', 'text', 'Como podemos ajudar'),
                            self::f('home.b2c.items', 'Itens do quadro', 'list', [
                                ['text' => 'Financiamento residencial e comercial'],
                                ['text' => 'Análise de crédito'],
                                ['text' => 'Orientação documental e registro'],
                                ['text' => 'Uso do FGTS, quando aplicável'],
                                ['text' => 'Home Equity, conforme elegibilidade'],
                                ['text' => 'Consórcios'],
                            ], ['item' => [self::f('text', 'Item', 'text', '')], 'add_label' => 'Adicionar item']),
                            self::f('home.b2c.note', 'Observação (letra pequena)', 'textarea', 'A aprovação, as taxas e as condições dependem da avaliação da instituição financeira.'),
                        ]
                    ),
                ],
                'b2b' => [
                    'title' => 'Para parceiros',
                    'fields' => array_merge(
                        self::head('home.b2b', 'Para parceiros · Mercado imobiliário', 'Mais suporte para quem faz o mercado imobiliário acontecer.', 'Conte com a experiência da DMR para apoiar seus clientes nas etapas de crédito imobiliário e facilitar a condução dos processos.'),
                        [
                            self::f('home.b2b.items', 'Cards', 'list', [
                                ['icon' => 'perfil', 'title' => 'Corretores', 'text' => 'Você cuida da negociação. A DMR acompanha todo o financiamento dos seus clientes.', 'link' => 'Conhecer a parceria', 'profile' => 'corretor'],
                                ['icon' => 'assessoria', 'title' => 'Imobiliárias', 'text' => 'Suporte especializado para sua equipe comercial e maior clareza na condução dos processos.', 'link' => 'Soluções para imobiliárias', 'profile' => 'imobiliaria'],
                                ['icon' => 'predio', 'title' => 'Construtoras e incorporadoras', 'text' => 'Expertise na operação de crédito, com profissionalismo e agilidade para manter em dia o fluxo de caixa do seu empreendimento.', 'link' => 'Conversar com a DMR', 'profile' => 'construtora'],
                            ], [
                                'item' => [
                                    self::f('icon', 'Ícone', 'icon', 'perfil'),
                                    self::f('title', 'Título', 'text', ''),
                                    self::f('text', 'Texto', 'textarea', ''),
                                    self::f('link', 'Texto do link', 'text', ''),
                                    self::f('profile', 'Ao clicar, abre o formulário de parceiro já com o perfil', 'select', '', ['options' => self::PARTNER_PROFILES]),
                                ],
                                'add_label' => 'Adicionar card',
                            ]),
                        ]
                    ),
                ],
                'steps' => [
                    'title' => 'Como funciona',
                    'fields' => array_merge(
                        self::head('home.steps', 'Nossa forma de trabalhar', 'Do primeiro contato à conclusão, você conta com a DMR.', 'Um processo de assessoria estruturado para compradores e parceiros, adaptado às necessidades de cada operação.'),
                        [
                            self::f('home.steps.items', 'Etapas', 'list', [
                                ['title' => 'Entendimento', 'text' => 'Conhecemos o perfil e os objetivos da operação.'],
                                ['title' => 'Análise', 'text' => 'Orientamos sobre alternativas e condições disponíveis.'],
                                ['title' => 'Documentação', 'text' => 'Apoiamos a preparação e o encaminhamento dos documentos.'],
                                ['title' => 'Acompanhamento', 'text' => 'Seguimos com você ao longo das etapas até a conclusão.'],
                            ], [
                                'item' => [self::f('title', 'Título', 'text', ''), self::f('text', 'Texto', 'textarea', '')],
                                'add_label' => 'Adicionar etapa',
                                'help' => 'A numeração (01, 02...) é automática.',
                            ]),
                        ]
                    ),
                ],
                'about' => [
                    'title' => 'Nossa história (ao lado da foto)',
                    'fields' => [
                        self::f('home.about.image', 'Foto', 'image', 'img/fundadora.jpg', ['help' => 'Vertical (retrato), de preferência 1080 × 1350 px.']),
                        self::f('home.about.image_alt', 'Descrição da foto (acessibilidade e Google)', 'text', 'Fundadora da DMR Assessoria Imobiliária'),
                        self::f('home.about.eyebrow', 'Chamada pequena', 'text', 'Nossa história'),
                        self::f('home.about.title', 'Título', 'rich', 'Uma trajetória construída junto com o mercado imobiliário.'),
                        self::f('home.about.text', 'Texto', 'paragraphs', "A DMR nasceu da trajetória da sua fundadora, que dedicou mais de 30 anos ao crédito imobiliário. Nesse tempo, ela acompanhou de perto as mudanças do mercado e aprendeu que, por trás de cada contrato, existe a conquista de uma família ou um negócio importante.\n\nDessa experiência surgiu a DMR: uma assessoria criada para tornar o financiamento mais simples, com atendimento próximo e alguém que conhece o processo do início ao fim. Ao longo do caminho, foram mais de 100 mil unidades financiadas.\n\nHoje, com matriz em São José do Rio Preto e filial em São Paulo, a DMR atende quem quer comprar o seu imóvel e também corretores, imobiliárias e construtoras que confiam no nosso trabalho.", ['help' => 'Deixe uma linha em branco entre os parágrafos.']),
                        self::f('home.about.button', 'Botão', 'text', 'Fale com nossa equipe'),
                    ],
                ],
                'purpose' => [
                    'title' => 'Nosso propósito',
                    'help' => 'O propósito substitui a seção "Missão, visão e valores". Se o texto do propósito ficar vazio, Missão, visão e valores volta a aparecer.',
                    'fields' => [
                        self::f('home.purpose.eyebrow', 'Chamada pequena (também usada no link do rodapé)', 'text', 'Nosso propósito'),
                        self::f('home.purpose.title', 'Título', 'rich', 'Cuidamos de pessoas. Simplificamos processos. Impulsionamos conquistas.'),
                        self::f('home.purpose.text', 'Texto do propósito', 'paragraphs', "Acreditamos que cada financiamento imobiliário representa mais do que uma operação financeira. É uma conquista importante para quem compra e uma oportunidade de crescimento para quem movimenta o mercado imobiliário.\n\nHá mais de 30 anos, combinamos experiência, atendimento próximo e processos estruturados para tornar essa jornada mais simples, transparente e eficiente.\n\nNosso compromisso é oferecer tranquilidade e orientação personalizada aos compradores, enquanto apoiamos corretores, imobiliárias e incorporadoras na condução de seus negócios com agilidade, segurança e responsabilidade.", ['help' => 'Deixe uma linha em branco entre os parágrafos. Vazio = volta a aparecer Missão, visão e valores.']),
                        self::f('home.purpose.pillars', 'Pilares (abaixo do texto)', 'list', [
                            ['icon' => 'cuidado', 'title' => 'Cuidado', 'text' => 'Pessoas em primeiro lugar'],
                            ['icon' => 'simplicidade', 'title' => 'Simplicidade', 'text' => 'Menos obstáculos, mais clareza'],
                            ['icon' => 'resultados', 'title' => 'Resultados', 'text' => 'Conquistas e negócios realizados'],
                        ], [
                            'item' => [
                                self::f('icon', 'Ícone', 'icon', 'cuidado'),
                                self::f('title', 'Título', 'text', ''),
                                self::f('text', 'Frase', 'text', ''),
                            ],
                            'add_label' => 'Adicionar pilar', 'max' => 4,
                        ]),
                    ],
                ],
                'mvv' => [
                    'title' => 'Missão, visão e valores (provisório)',
                    'help' => 'Some do site automaticamente quando o propósito for preenchido.',
                    'fields' => array_merge(
                        self::head('home.mvv', 'Nossa essência', 'O que nos orienta', 'Acreditamos que a experiência faz a diferença quando é acompanhada de cuidado, transparência e compromisso com cada conquista.'),
                        [
                            self::f('home.mvv.mission_title', 'Missão — título', 'text', 'Nossa Missão'),
                            self::f('home.mvv.mission_text', 'Missão — texto', 'textarea', 'Simplificar o financiamento imobiliário com atendimento próximo, orientação especializada e processos eficientes, proporcionando tranquilidade aos compradores e resultados aos nossos parceiros.'),
                            self::f('home.mvv.vision_title', 'Visão — título', 'text', 'Nossa Visão'),
                            self::f('home.mvv.vision_text', 'Visão — texto', 'textarea', 'Ser referência em atendimento e eficiência no crédito imobiliário, ampliando parcerias e tornando o acesso ao financiamento cada vez mais simples e acessível.'),
                            self::f('home.mvv.values_title', 'Valores — título', 'text', 'Nossos Valores'),
                            self::f('home.mvv.values', 'Valores', 'list', [
                                ['title' => 'Honestidade', 'text' => 'Agimos com integridade.'],
                                ['title' => 'Transparência', 'text' => 'Comunicamos com clareza.'],
                                ['title' => 'Comprometimento', 'text' => 'Assumimos cada processo com responsabilidade.'],
                                ['title' => 'Profissionalismo', 'text' => 'Unimos experiência e organização.'],
                                ['title' => 'Cuidado com as pessoas', 'text' => 'Atendemos com atenção e respeito.'],
                                ['title' => 'Eficiência', 'text' => 'Valorizamos o tempo e os recursos de todos.'],
                            ], ['item' => [self::f('title', 'Valor', 'text', ''), self::f('text', 'Descrição', 'text', '')], 'add_label' => 'Adicionar valor']),
                            self::f('home.mvv.quote', 'Frase de destaque', 'textarea', 'Cuidamos de pessoas. Simplificamos processos. Impulsionamos conquistas.'),
                            self::f('home.mvv.details_label', 'Texto do botão "ver mais"', 'text', 'Conheça nossos compromissos em mais detalhes'),
                            self::f('home.mvv.details', 'Texto detalhado (abre ao clicar)', 'markup', self::mvvDetailsDefault()),
                        ]
                    ),
                ],
                'banks' => [
                    'title' => 'Instituições financeiras (logos)',
                    'fields' => array_merge(
                        self::head('home.banks', 'Relacionamento financeiro', 'Conexões com instituições financeiras do mercado.', 'As alternativas de financiamento são avaliadas conforme as características da operação e os critérios de cada instituição.'),
                        [
                            self::f('home.banks.items', 'Logos', 'list', [
                                ['image' => 'img/banks/caixa.svg', 'name' => 'Caixa Econômica Federal', 'square' => '0'],
                                ['image' => 'img/banks/santander.svg', 'name' => 'Santander', 'square' => '0'],
                                ['image' => 'img/banks/bradesco.svg', 'name' => 'Bradesco', 'square' => '0'],
                                ['image' => 'img/banks/itau.svg', 'name' => 'Itaú', 'square' => '1'],
                                ['image' => 'img/banks/consorcio.svg', 'name' => 'Consórcios', 'square' => '0'],
                            ], [
                                'item' => [
                                    self::f('image', 'Logo', 'image', ''),
                                    self::f('name', 'Nome', 'text', ''),
                                    self::f('square', 'Logo quadrado (ex.: Itaú)', 'bool', '0'),
                                ],
                                'add_label' => 'Adicionar logo',
                                'help' => 'Prefira SVG ou PNG com fundo transparente. O site deixa os logos em cinza e mostra a cor ao passar o mouse.',
                            ]),
                        ]
                    ),
                ],
                'testimonials' => [
                    'title' => 'Depoimentos',
                    'fields' => array_merge(
                        self::head('home.testimonials', 'Histórias e confiança', 'Relacionamentos que fazem a diferença.', 'A experiência de quem já contou com a DMR ajuda novos compradores e parceiros a conhecer nosso trabalho.'),
                        [
                            self::f('home.testimonials.items', 'Depoimentos', 'list', [
                                ['text' => 'Achei que financiar o primeiro apartamento seria uma dor de cabeça. A equipe da DMR explicou cada etapa, organizou a documentação com a gente e acompanhou tudo até a assinatura no banco.', 'name' => 'Mariana S.', 'role' => 'Compradora · São José do Rio Preto/SP'],
                                ['text' => 'Encaminho meus clientes para a DMR há anos. O retorno é rápido, o processo é transparente e eu consigo focar na negociação sabendo que o crédito está em boas mãos.', 'name' => 'Ricardo A.', 'role' => 'Corretor de imóveis'],
                                ['text' => 'Usamos o FGTS na compra da casa e tínhamos muitas dúvidas. Fomos atendidos com paciência e clareza do começo ao fim. Recomendamos para toda a família.', 'name' => 'Juliana e Fernando P.', 'role' => 'Compradores · Mirassol/SP'],
                            ], [
                                'item' => [
                                    self::f('text', 'Depoimento', 'textarea', ''),
                                    self::f('name', 'Nome', 'text', ''),
                                    self::f('role', 'Perfil / cidade', 'text', ''),
                                ],
                                'add_label' => 'Adicionar depoimento',
                                'help' => 'Sem depoimentos, a seção não aparece no site.',
                            ]),
                        ]
                    ),
                ],
                'faq' => [
                    'title' => 'Perguntas frequentes',
                    'fields' => array_merge(
                        self::head('home.faq', 'Dúvidas frequentes', 'Respostas para começar com confiança.'),
                        [
                            self::f('home.faq.items', 'Perguntas', 'list', [
                                ['q' => 'Posso falar com a DMR diretamente, sem um corretor?', 'a' => 'Sim. Compradores podem solicitar atendimento diretamente para conhecer as possibilidades de assessoria em crédito imobiliário.'],
                                ['q' => 'Sou corretor ou representante de imobiliária. Como funciona a parceria?', 'a' => 'Entre em contato pelo formulário de parceiros. A equipe poderá conhecer sua atuação e explicar as modalidades de suporte disponíveis.'],
                                ['q' => 'A DMR garante a aprovação do financiamento?', 'a' => 'Não. A aprovação e as condições dependem dos critérios e da análise da instituição financeira responsável.'],
                            ], [
                                'item' => [self::f('q', 'Pergunta', 'text', ''), self::f('a', 'Resposta', 'textarea', '')],
                                'add_label' => 'Adicionar pergunta',
                            ]),
                        ]
                    ),
                ],
                'contact' => [
                    'title' => 'Contato e formulários',
                    'help' => 'Telefones, endereço e Instagram ficam em Configurações → Geral.',
                    'fields' => [
                        self::f('home.contact.eyebrow', 'Chamada pequena', 'text', 'Vamos conversar'),
                        self::f('home.contact.title', 'Título', 'rich', 'Seu próximo passo merece a experiência certa.'),
                        self::f('home.contact.text', 'Texto', 'textarea', 'Seja para financiar um imóvel ou apoiar seus negócios imobiliários, escolha seu perfil e conte com a DMR.'),
                        self::f('home.contact.tab_b2c', 'Aba 1', 'text', 'Quero financiar'),
                        self::f('home.contact.tab_b2b', 'Aba 2', 'text', 'Sou parceiro'),
                        self::f('home.contact.b2c_name', 'Comprador — campo nome', 'text', 'Seu nome'),
                        self::f('home.contact.phone', 'Campo WhatsApp', 'text', 'WhatsApp'),
                        self::f('home.contact.email', 'Comprador — campo e-mail', 'text', 'E-mail'),
                        self::f('home.contact.city', 'Campo cidade', 'text', 'Cidade / Estado'),
                        self::f('home.contact.interest', 'Comprador — campo "o que procura"', 'text', 'O que você procura?'),
                        self::f('home.contact.interests', 'Comprador — opções de "o que procura"', 'list', [
                            ['label' => 'Financiamento residencial'],
                            ['label' => 'Financiamento comercial'],
                            ['label' => 'Uso do FGTS'],
                            ['label' => 'Home Equity'],
                            ['label' => 'Consórcio'],
                            ['label' => 'Outra orientação'],
                        ], ['item' => [self::f('label', 'Opção', 'text', '')], 'add_label' => 'Adicionar opção']),
                        self::f('home.contact.message', 'Comprador — campo mensagem', 'text', 'Mensagem'),
                        self::f('home.contact.b2c_note', 'Comprador — aviso', 'text', 'Não envie CPF, renda ou documentos por este formulário.'),
                        self::f('home.contact.b2c_button', 'Comprador — botão', 'text', 'Solicitar orientação'),
                        self::f('home.contact.b2b_name', 'Parceiro — campo nome', 'text', 'Nome do responsável'),
                        self::f('home.contact.b2b_profile', 'Parceiro — campo perfil', 'text', 'Perfil profissional'),
                        self::f('home.contact.profile_corretor', 'Parceiro — opção corretor', 'text', 'Corretor autônomo'),
                        self::f('home.contact.profile_imobiliaria', 'Parceiro — opção imobiliária', 'text', 'Imobiliária'),
                        self::f('home.contact.profile_construtora', 'Parceiro — opção construtora', 'text', 'Construtora ou incorporadora'),
                        self::f('home.contact.b2b_company', 'Parceiro — campo empresa', 'text', 'Empresa'),
                        self::f('home.contact.b2b_email', 'Parceiro — campo e-mail', 'text', 'E-mail comercial'),
                        self::f('home.contact.b2b_message', 'Parceiro — campo mensagem', 'text', 'Como podemos ajudar?'),
                        self::f('home.contact.b2b_button', 'Parceiro — botão', 'text', 'Solicitar contato comercial'),
                        self::f('home.contact.privacy', 'Frase da política (abaixo do botão)', 'paragraphs', 'Ao enviar, você concorda com a [Política de Privacidade](/politica-de-privacidade).'),
                        self::f('home.contact.success', 'Mensagem de envio com sucesso', 'text', 'Recebemos sua mensagem. A DMR entrará em contato em breve.'),
                    ],
                ],
            ],
        ];
    }

    private static function mvvDetailsDefault(): string
    {
        return <<<TXT
## Nosso propósito

Na DMR, acreditamos que financiar um imóvel deve ser uma experiência conduzida com clareza, segurança e tranquilidade. Com mais de 30 anos de experiência no mercado imobiliário, construímos nossa trajetória unindo conhecimento técnico, atendimento humano e processos estruturados. Por trás de cada financiamento existe uma conquista pessoal, uma decisão importante e uma relação de confiança.

Para nossos clientes, buscamos simplificar cada etapa, evitando burocracias desnecessárias e oferecendo orientação próxima e personalizada. Para nossos parceiros, trabalhamos para tornar os processos mais eficientes, apoiar a satisfação do comprador e contribuir para a conclusão das operações com agilidade e previsibilidade.

## Missão — compromisso diário

Simplificar e facilitar o acesso ao financiamento imobiliário por meio de orientação especializada, atendimento próximo e processos estruturados e eficientes. Atuamos para proporcionar aos compradores uma experiência transparente, segura e tranquila, preservando seu tempo e seus recursos. Ao mesmo tempo, apoiamos nossos parceiros com agilidade, comprometimento e acompanhamento responsável, contribuindo para a conclusão das operações e para a eficiência de seus negócios.

## Visão — onde queremos chegar

Ser reconhecida como referência em atendimento ao cliente, confiança e eficiência no mercado de crédito imobiliário. Buscamos expandir nossa rede de parceiros, fortalecer relacionamentos de longo prazo e contribuir para um mercado imobiliário mais acessível, transparente e eficiente, no qual compradores e profissionais encontrem na DMR uma parceira de confiança para suas conquistas e negócios.

## Valores — como agimos

- **Honestidade e integridade:** agimos com ética e responsabilidade em todas as relações, fazendo o que é correto mesmo diante de desafios.
- **Transparência:** comunicamos condições, possibilidades, etapas e desafios com sinceridade para apoiar decisões conscientes.
- **Comprometimento:** acompanhamos cada operação com dedicação, responsabilidade e respeito aos compromissos estabelecidos.
- **Profissionalismo:** unimos experiência, conhecimento técnico e processos estruturados para oferecer atendimento organizado e confiável.
- **Cuidado com as pessoas:** valorizamos a escuta, a educação, a empatia e o atendimento personalizado.
- **Eficiência com responsabilidade:** buscamos reduzir obstáculos e retrabalho, respeitando o tempo e os recursos envolvidos sem comprometer a segurança ou a qualidade.
TXT;
    }

    // --- Cabeçalho e rodapé ------------------------------------------------

    /** @return array<string,mixed> */
    private static function layoutPage(): array
    {
        return [
            'title' => 'Menu, rodapé e WhatsApp',
            'description' => 'Partes que aparecem em todas as páginas.',
            'url' => '',
            'sections' => [
                'menu' => [
                    'title' => 'Menu do topo',
                    'help' => 'A logo é trocada em Configurações → Geral.',
                    'fields' => [
                        self::f('layout.menu.b2c', 'Link 1', 'text', 'Para você'),
                        self::f('layout.menu.b2b', 'Link 2', 'text', 'Para parceiros'),
                        self::f('layout.menu.steps', 'Link 3', 'text', 'Como funciona'),
                        self::f('layout.menu.about', 'Link 4', 'text', 'Sobre a DMR'),
                        self::f('layout.menu.cta', 'Botão de destaque', 'text', 'Fale com a DMR'),
                    ],
                ],
                'footer' => [
                    'title' => 'Rodapé',
                    'fields' => [
                        self::f('layout.footer.tagline', 'Frase abaixo do nome (quando não há logo)', 'text', 'Assessoria Imobiliária'),
                        self::f('layout.footer.desc', 'Frase do rodapé', 'textarea', 'Experiência que conecta pessoas, negócios e conquistas.'),
                        self::f('layout.footer.nav_title', 'Título da coluna de links', 'text', 'Navegação'),
                        self::f('layout.footer.nav_purpose', 'Link "O que nos orienta"', 'text', 'O que nos orienta', ['help' => 'Usado só quando o propósito está vazio. Com o propósito publicado, o link mostra a chamada pequena do propósito.']),
                        self::f('layout.footer.nav_contact', 'Link "Contato"', 'text', 'Contato'),
                        self::f('layout.footer.contact_title', 'Título da coluna de contato', 'text', 'Contato'),
                        self::f('layout.footer.legal_title', 'Título da coluna institucional', 'text', 'Institucional'),
                        self::f('layout.footer.copyright', 'Texto de direitos', 'text', 'Todos os direitos reservados.'),
                    ],
                ],
                'whatsapp' => [
                    'title' => 'Botão flutuante do WhatsApp',
                    'fields' => [
                        self::f('layout.whatsapp.title', 'Título da janelinha (matriz/filial)', 'text', 'Fale pelo WhatsApp'),
                        self::f('layout.whatsapp.message', 'Mensagem que já vem escrita', 'text', 'Olá, gostaria de falar com a DMR sobre crédito imobiliário.'),
                    ],
                ],
            ],
        ];
    }

    // --- Páginas legais ----------------------------------------------------

    private static function legalHelp(): string
    {
        return 'Formatação: "## " no início da linha cria um título; "- " cria um item de lista; linha em branco separa parágrafos; **texto** fica em negrito; [texto](/endereco) vira link. '
            . 'Códigos que o site preenche sozinho: {empresa}, {email}, {telefones}, {unidades}.';
    }

    /** @return array<string,mixed> */
    private static function privacyPage(): array
    {
        return [
            'title' => 'Política de Privacidade',
            'description' => 'Texto completo da página /politica-de-privacidade.',
            'url' => 'politica-de-privacidade',
            'sections' => [
                'main' => [
                    'title' => 'Conteúdo',
                    'fields' => [
                        self::f('privacy.title', 'Título', 'text', 'Política de Privacidade'),
                        self::f('privacy.updated', 'Data da última atualização', 'text', '', ['help' => 'Vazio = usa a data em que o texto foi salvo pela última vez.']),
                        self::f('privacy.notice', 'Aviso em destaque', 'paragraphs', '**Aviso importante:** este documento é um modelo de referência e deve ser revisado por um profissional jurídico antes da publicação definitiva, de forma a refletir com precisão as práticas da {empresa} e as exigências da Lei Geral de Proteção de Dados (Lei nº 13.709/2018 — LGPD).', ['help' => 'Apague o texto para esconder o aviso.']),
                        self::f('privacy.body', 'Texto da política', 'markup', self::privacyDefault(), ['help' => self::legalHelp()]),
                    ],
                ],
            ],
        ];
    }

    private static function privacyDefault(): string
    {
        return <<<TXT
A {empresa} ("DMR", "nós") valoriza a privacidade de seus visitantes, clientes e parceiros. Esta Política de Privacidade descreve como coletamos, utilizamos, armazenamos e protegemos os dados pessoais tratados por meio deste site e dos canais de atendimento da empresa.

{unidades} Esta Política vale para os atendimentos das duas unidades.

## 1. Dados que coletamos

Podemos coletar as seguintes categorias de dados:

- **Dados fornecidos por você:** nome, e-mail, telefone/WhatsApp, tipo de atendimento e o conteúdo das mensagens enviadas por meio dos formulários do site.
- **Dados de navegação:** endereço IP, informações do dispositivo e do navegador, páginas acessadas e dados de cookies, quando aplicável.
- **Dados necessários à prestação de serviços:** informações relacionadas a crédito, financiamento e documentação, coletadas no contexto de uma contratação ou atendimento, sempre com a devida base legal.

## 2. Finalidade do tratamento

Utilizamos os dados pessoais para:

- responder a solicitações de contato e orçamento;
- prestar serviços de assessoria em crédito imobiliário e acompanhar processos;
- comunicar informações relevantes sobre o andamento de atendimentos;
- cumprir obrigações legais e regulatórias;
- aprimorar a experiência de navegação e os nossos serviços.

## 3. Base legal

O tratamento de dados é realizado com fundamento nas hipóteses previstas na LGPD, tais como o consentimento do titular, a execução de contrato, o cumprimento de obrigação legal e o legítimo interesse, sempre respeitando os direitos e as liberdades fundamentais do titular.

## 4. Formulários e contato

Ao preencher um formulário no site, você autoriza o uso dos dados informados para que a DMR possa entrar em contato e dar andamento ao atendimento solicitado. Esses dados não são utilizados para finalidades incompatíveis com aquelas informadas no momento da coleta.

## 5. Cookies

Este site pode utilizar cookies e tecnologias semelhantes para garantir o seu funcionamento, lembrar preferências e, eventualmente, mensurar o desempenho das páginas. Você pode gerenciar ou desativar cookies nas configurações do seu navegador, ciente de que isso pode afetar algumas funcionalidades.

## 6. Compartilhamento de dados

A DMR poderá compartilhar dados pessoais com instituições financeiras, bancos parceiros, cartórios e prestadores de serviço estritamente na medida necessária à execução do atendimento ou da contratação, bem como para o cumprimento de obrigações legais. Não comercializamos dados pessoais.

## 7. Armazenamento e segurança

Adotamos medidas técnicas e organizacionais razoáveis para proteger os dados pessoais contra acessos não autorizados, perda, alteração ou divulgação indevida. Os dados são mantidos pelo tempo necessário ao cumprimento das finalidades informadas ou das obrigações legais aplicáveis.

## 8. Direitos do titular

Nos termos da LGPD, você pode, a qualquer momento, solicitar:

- a confirmação da existência de tratamento;
- o acesso aos seus dados;
- a correção de dados incompletos, inexatos ou desatualizados;
- a anonimização, o bloqueio ou a eliminação de dados desnecessários;
- a portabilidade e a informação sobre compartilhamento;
- a revogação do consentimento, quando aplicável.

## 9. Contato do responsável

Para exercer seus direitos ou esclarecer dúvidas sobre esta Política, entre em contato pelo e-mail {email} ou pelos telefones {telefones}.

## 10. Atualizações desta Política

Esta Política de Privacidade pode ser atualizada periodicamente. Recomendamos a sua consulta regular. Alterações relevantes poderão ser comunicadas pelos canais oficiais da DMR.
TXT;
    }

    /** @return array<string,mixed> */
    private static function termsPage(): array
    {
        return [
            'title' => 'Termos de Uso',
            'description' => 'Texto completo da página /termos-de-uso.',
            'url' => 'termos-de-uso',
            'sections' => [
                'main' => [
                    'title' => 'Conteúdo',
                    'fields' => [
                        self::f('terms.title', 'Título', 'text', 'Termos de Uso'),
                        self::f('terms.updated', 'Data da última atualização', 'text', '', ['help' => 'Vazio = usa a data em que o texto foi salvo pela última vez.']),
                        self::f('terms.notice', 'Aviso em destaque', 'paragraphs', '**Aviso importante:** este documento é um modelo de referência e deve ser revisado por um profissional jurídico antes da publicação definitiva, para refletir com precisão a atuação da {empresa} e a legislação aplicável.', ['help' => 'Apague o texto para esconder o aviso.']),
                        self::f('terms.body', 'Texto dos termos', 'markup', self::termsDefault(), ['help' => self::legalHelp()]),
                    ],
                ],
            ],
        ];
    }

    private static function termsDefault(): string
    {
        return <<<TXT
Estes Termos de Uso regulam o acesso e a utilização do site da {empresa}. Ao navegar neste site, você declara estar ciente e de acordo com as condições a seguir.

## 1. Objeto

Este site tem caráter informativo e institucional, apresentando os serviços de assessoria em crédito imobiliário prestados pela DMR e disponibilizando canais de contato. As informações aqui publicadas não constituem oferta, promessa de aprovação de crédito ou garantia de condições específicas.

## 2. Uso do site

Ao utilizar este site, você se compromete a:

- fornecer informações verdadeiras nos formulários de contato;
- não utilizar o site para fins ilícitos ou que violem direitos de terceiros;
- não tentar obter acesso não autorizado a áreas restritas, sistemas ou dados;
- não realizar atividades que possam comprometer a segurança ou o funcionamento do site.

## 3. Informações sobre crédito e financiamento

As condições de crédito, taxas, prazos e aprovações dependem de análise individual e das políticas das instituições financeiras envolvidas. Nenhuma informação apresentada no site deve ser interpretada como aprovação prévia ou garantia de contratação. Cada caso é avaliado conforme o perfil do interessado.

## 4. Propriedade intelectual

Os textos, marcas, logotipos, layout e demais elementos deste site são protegidos por direitos de propriedade intelectual e pertencem à DMR ou a seus respectivos titulares. É vedada a reprodução, distribuição ou utilização sem autorização prévia e expressa.

## 5. Links para terceiros

O site pode conter links para páginas de terceiros, como instituições financeiras e redes sociais. A DMR não se responsabiliza pelo conteúdo, pelas políticas ou pelas práticas desses sites externos.

## 6. Limitação de responsabilidade

A DMR empenha-se para manter as informações do site corretas e atualizadas, mas não garante a ausência de eventuais imprecisões ou indisponibilidades temporárias. O uso das informações é de responsabilidade do usuário.

## 7. Privacidade

O tratamento de dados pessoais realizado por meio do site é regido pela nossa [Política de Privacidade](/politica-de-privacidade), que integra estes Termos de Uso.

## 8. Alterações

A DMR pode modificar estes Termos de Uso a qualquer momento. A versão vigente é sempre a publicada nesta página, com a data de atualização indicada no topo.

## 9. Contato

Em caso de dúvidas sobre estes Termos, entre em contato pelo e-mail {email} ou pelos telefones {telefones}.
TXT;
    }

    // --- Páginas de erro ---------------------------------------------------

    /** @return array<string,mixed> */
    private static function errorsPage(): array
    {
        return [
            'title' => 'Páginas de erro',
            'description' => 'Mensagens mostradas quando uma página não existe ou ocorre uma falha.',
            'url' => '',
            'sections' => [
                'e404' => [
                    'title' => 'Página não encontrada (404)',
                    'fields' => [
                        self::f('errors.404.title', 'Título', 'text', 'Página não encontrada'),
                        self::f('errors.404.text', 'Texto', 'textarea', 'O endereço que você procura não existe ou foi movido.'),
                        self::f('errors.button', 'Botão (404 e 500)', 'text', 'Voltar ao início'),
                    ],
                ],
                'e500' => [
                    'title' => 'Erro inesperado (500)',
                    'fields' => [
                        self::f('errors.500.title', 'Título', 'text', 'Algo deu errado'),
                        self::f('errors.500.text', 'Texto', 'textarea', 'Ocorreu um erro inesperado. Tente novamente em instantes.'),
                    ],
                ],
            ],
        ];
    }

    // --- SEO ---------------------------------------------------------------

    /** @return array<string,mixed> */
    private static function seoPage(): array
    {
        $titleHelp = 'Aparece na aba do navegador e no Google. Ideal: até 60 caracteres.';
        $descHelp = 'Resumo que aparece no Google e ao compartilhar o link. Ideal: até 160 caracteres.';
        return [
            'title' => 'Google e compartilhamento (SEO)',
            'description' => 'Títulos, descrições e imagem usados pelo Google, WhatsApp e redes sociais.',
            'url' => '',
            'sections' => [
                'home' => [
                    'title' => 'Página inicial',
                    'fields' => [
                        self::f('seo.home.title', 'Título', 'text', 'DMR Assessoria Imobiliária | Crédito Imobiliário para Você e para Parceiros', ['help' => $titleHelp]),
                        self::f('seo.home.description', 'Descrição', 'textarea', 'Assessoria especializada em crédito imobiliário para compradores, corretores, imobiliárias e construtoras. Mais de 30 anos de experiência. São José do Rio Preto/SP.', ['help' => $descHelp]),
                        self::f('seo.og_image', 'Imagem ao compartilhar o link', 'image', 'img/og-image.svg', ['help' => 'Use JPG ou PNG de 1200 × 630 px. O WhatsApp não mostra SVG.']),
                    ],
                ],
                'privacy' => [
                    'title' => 'Política de Privacidade',
                    'fields' => [
                        self::f('seo.privacy.title', 'Título', 'text', 'Política de Privacidade | DMR Assessoria Imobiliária', ['help' => $titleHelp]),
                        self::f('seo.privacy.description', 'Descrição', 'textarea', 'Saiba como a DMR Assessoria Imobiliária coleta, utiliza e protege seus dados pessoais.', ['help' => $descHelp]),
                    ],
                ],
                'terms' => [
                    'title' => 'Termos de Uso',
                    'fields' => [
                        self::f('seo.terms.title', 'Título', 'text', 'Termos de Uso | DMR Assessoria Imobiliária', ['help' => $titleHelp]),
                        self::f('seo.terms.description', 'Descrição', 'textarea', 'Condições de uso do site da DMR Assessoria Imobiliária.', ['help' => $descHelp]),
                    ],
                ],
            ],
        ];
    }
}
