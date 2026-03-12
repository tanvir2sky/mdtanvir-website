@extends('layouts.app')

@section('content')
<nav
      class="fixed top-0 w-full z-50 bg-white/80 dark:bg-gray-950/80 backdrop-blur-xl border-b border-gray-200/70 dark:border-gray-800/70 transition-colors duration-300"
    >
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <a
            href="#home"
            class="text-lg font-extrabold tracking-tight text-primary-700 dark:text-primary-300"
          >
            MD Tanvir
          </a>
          <div class="hidden md:flex items-center space-x-7">
            <a
              href="#home"
              class="nav-link text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors"
              >Home</a
            >
            <a
              href="#about"
              class="nav-link text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors"
              >About</a
            >
            <a
              href="#skills"
              class="nav-link text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors"
              >Skills</a
            >
            <a
              href="#experience"
              class="nav-link text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors"
              >Experience</a
            >
            <a
              href="#projects"
              class="nav-link text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors"
              >Projects</a
            >
            <a
              href="#contact"
              class="nav-link text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors"
              >Contact</a
            >
          </div>

          <div class="flex items-center gap-2">
            <button
              id="theme-toggle"
              aria-label="Toggle theme"
              class="p-2 rounded-lg bg-gray-200 dark:bg-gray-800 hover:bg-gray-300 dark:hover:bg-gray-700 transition-colors"
            >
              <i class="fas fa-moon dark:hidden"></i>
              <i class="fas fa-sun hidden dark:inline"></i>
            </button>
            <button
              id="mobile-menu-toggle"
              aria-label="Toggle mobile menu"
              class="md:hidden p-2 rounded-lg bg-gray-200 dark:bg-gray-800 hover:bg-gray-300 dark:hover:bg-gray-700 transition-colors"
            >
              <i class="fas fa-bars"></i>
            </button>
          </div>
        </div>
      </div>

      <div
        id="mobile-menu"
        class="hidden md:hidden border-t border-gray-200 dark:border-gray-800 bg-white/95 dark:bg-gray-950/95"
      >
        <div class="px-4 py-4 space-y-3">
          <a
            href="#home"
            class="block nav-link text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400"
            >Home</a
          >
          <a
            href="#about"
            class="block nav-link text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400"
            >About</a
          >
          <a
            href="#skills"
            class="block nav-link text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400"
            >Skills</a
          >
          <a
            href="#experience"
            class="block nav-link text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400"
            >Experience</a
          >
          <a
            href="#projects"
            class="block nav-link text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400"
            >Projects</a
          >
          <a
            href="#contact"
            class="block nav-link text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400"
            >Contact</a
          >
        </div>
      </div>
    </nav>

    <div id="particles-js" class="fixed inset-0 z-0 pointer-events-none"></div>

    <main class="relative z-10">
      <section
        id="home"
        class="min-h-screen flex items-center px-4 sm:px-6 lg:px-8 pt-20"
      >
        <div class="max-w-7xl mx-auto w-full">
          <div class="grid lg:grid-cols-12 gap-10 xl:gap-14 items-center">
            <div class="lg:col-span-7 animate-fade-in">
              <p
                class="inline-flex items-center gap-2 px-4 py-1.5 mb-5 rounded-full text-sm font-medium bg-primary-100/80 text-primary-800 dark:bg-primary-900/40 dark:text-primary-200 border border-primary-200 dark:border-primary-800 animate-pulse-slow"
              >
                <span class="w-2 h-2 rounded-full bg-primary-500"></span>
                Open to impactful engineering work
              </p>
              <h1
                class="text-4xl sm:text-5xl lg:text-6xl xl:text-7xl font-black tracking-tight mb-5 bg-gradient-to-r from-primary-700 via-primary-500 to-cyan-400 bg-clip-text text-transparent animate-slide-up"
              >
                MD Tanvir Hossain
              </h1>
              <h2
                class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-gray-200 mb-5 animate-slide-up"
                style="animation-delay: 0.1s"
              >
                Software Engineer
              </h2>
              <p
                class="text-lg md:text-xl leading-relaxed text-gray-600 dark:text-gray-300 max-w-2xl mb-8 animate-slide-up"
                style="animation-delay: 0.2s"
              >
                Crafting robust web solutions with Laravel, PHP, and Shopify.
                Passionate about clean code, scalable architecture, and
                delivering exceptional digital experiences.
              </p>

              <div
                class="flex flex-wrap gap-3 sm:gap-4 animate-slide-up"
                style="animation-delay: 0.3s"
              >
                <a
                  href="#contact"
                  class="px-6 sm:px-8 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-semibold shadow-soft hover:shadow-xl transition-all transform hover:-translate-y-0.5"
                >
                  Get In Touch
                </a>
                <a
                  href="https://www.linkedin.com/in/tanvir-cs"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="px-6 sm:px-8 py-3 rounded-xl font-semibold border border-gray-300 dark:border-gray-700 bg-white/80 dark:bg-gray-900/80 hover:bg-gray-100 dark:hover:bg-gray-800 transition-all"
                >
                  <i class="fab fa-linkedin mr-2 text-primary-600 dark:text-primary-400"></i>LinkedIn
                </a>
                <a
                  href="https://github.com/tanvir-cs"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="px-6 sm:px-8 py-3 rounded-xl font-semibold border border-gray-300 dark:border-gray-700 bg-white/80 dark:bg-gray-900/80 hover:bg-gray-100 dark:hover:bg-gray-800 transition-all"
                >
                  <i class="fab fa-github mr-2 text-primary-600 dark:text-primary-400"></i>GitHub
                </a>
              </div>

              <div class="mt-10 grid sm:grid-cols-3 gap-4 max-w-2xl">
                <div
                  class="bg-white/80 dark:bg-gray-900/70 border border-gray-200 dark:border-gray-800 rounded-xl p-4"
                >
                  <p class="text-2xl font-extrabold text-primary-700 dark:text-primary-300">
                    8+
                  </p>
                  <p class="text-sm text-gray-600 dark:text-gray-400">
                    Years in web development
                  </p>
                </div>
                <div
                  class="bg-white/80 dark:bg-gray-900/70 border border-gray-200 dark:border-gray-800 rounded-xl p-4"
                >
                  <p class="text-2xl font-extrabold text-primary-700 dark:text-primary-300">
                    3
                  </p>
                  <p class="text-sm text-gray-600 dark:text-gray-400">
                    Core specialization areas
                  </p>
                </div>
                <div
                  class="bg-white/80 dark:bg-gray-900/70 border border-gray-200 dark:border-gray-800 rounded-xl p-4"
                >
                  <p class="text-2xl font-extrabold text-primary-700 dark:text-primary-300">
                    Global
                  </p>
                  <p class="text-sm text-gray-600 dark:text-gray-400">
                    Cross-functional collaboration
                  </p>
                </div>
              </div>
            </div>

            <div class="lg:col-span-5">
              <div
                class="relative max-w-md mx-auto animate-fade-in"
                style="animation-delay: 0.2s"
              >
                <div
                  class="absolute -inset-2 bg-gradient-to-br from-primary-500/50 to-cyan-400/40 blur-2xl rounded-[2rem]"
                ></div>
                <div
                  class="relative bg-white/85 dark:bg-gray-900/80 rounded-[2rem] p-7 border border-gray-200 dark:border-gray-800 shadow-2xl"
                >
                  <img
                    src="{{ asset('img/profile.jpg') }}"
                    alt="MD Tanvir Hossain"
                    class="w-36 h-36 sm:w-44 sm:h-44 mx-auto rounded-full object-cover border-4 border-primary-200 dark:border-primary-800 shadow-xl animate-float"
                  />
                  <div class="mt-6 text-center">
                    <p class="text-xl font-bold text-gray-900 dark:text-white">
                      Laravel | PHP | Shopify
                    </p>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                      Building secure, scalable, and high-performance products.
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section
        id="about"
        class="py-20 sm:py-24 px-4 sm:px-6 lg:px-8 bg-gray-50/80 dark:bg-gray-900/70"
      >
        <div class="max-w-6xl mx-auto">
          <div class="grid lg:grid-cols-12 gap-10">
            <div class="lg:col-span-4">
              <p
                class="text-sm font-semibold uppercase tracking-wider text-primary-700 dark:text-primary-300 mb-3"
              >
                About Me
              </p>
              <h2 class="text-4xl md:text-5xl font-black text-gray-900 dark:text-white">
                Engineer focused on quality and scale
              </h2>
            </div>
            <div class="lg:col-span-8 space-y-5">
              <p class="text-lg text-gray-700 dark:text-gray-300 leading-relaxed">
                I'm a passionate Software Engineer with expertise in building
                scalable web applications using modern technologies. My journey
                in software development has been driven by a commitment to
                writing clean, maintainable code and solving complex problems
                with elegant solutions.
              </p>
              <p class="text-lg text-gray-700 dark:text-gray-300 leading-relaxed">
                Specializing in
                <strong class="text-primary-600 dark:text-primary-400"
                  >Laravel</strong
                >
                and
                <strong class="text-primary-600 dark:text-primary-400">PHP</strong>,
                I've developed robust backend systems and RESTful APIs that
                power high-performance applications. My experience with
                <strong class="text-primary-600 dark:text-primary-400"
                  >Shopify</strong
                >
                has enabled me to create seamless e-commerce solutions and
                custom storefronts for businesses of all sizes.
              </p>
              <p class="text-lg text-gray-700 dark:text-gray-300 leading-relaxed">
                Beyond coding, I'm dedicated to continuous learning, staying
                updated with industry best practices, and contributing to the
                developer community. I believe in building software that not
                only meets requirements but exceeds expectations in terms of
                performance, security, and user experience.
              </p>
            </div>
          </div>
        </div>
      </section>

      <section id="skills" class="py-20 sm:py-24 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
          <div class="text-center mb-12">
            <p
              class="text-sm font-semibold uppercase tracking-wider text-primary-700 dark:text-primary-300 mb-3"
            >
              Skills & Technologies
            </p>
            <h2 class="text-4xl md:text-5xl font-black text-gray-900 dark:text-white">
              Technical strengths
            </h2>
          </div>

          <div class="grid md:grid-cols-2 xl:grid-cols-4 gap-6">
            <article
              class="skill-card bg-white/80 dark:bg-gray-900/80 p-6 rounded-2xl shadow-lg hover:shadow-2xl transition-all transform hover:-translate-y-1 border border-gray-200 dark:border-gray-800"
            >
              <div class="text-3xl mb-4 text-primary-600 dark:text-primary-400">
                <i class="fas fa-server"></i>
              </div>
              <h3 class="text-xl font-bold mb-4 text-gray-900 dark:text-white">
                Backend
              </h3>
              <ul class="space-y-2 text-gray-700 dark:text-gray-300">
                <li class="flex items-center"><i class="fas fa-check-circle text-primary-600 dark:text-primary-400 mr-2"></i>Laravel</li>
                <li class="flex items-center"><i class="fas fa-check-circle text-primary-600 dark:text-primary-400 mr-2"></i>PHP</li>
                <li class="flex items-center"><i class="fas fa-check-circle text-primary-600 dark:text-primary-400 mr-2"></i>RESTful APIs</li>
                <li class="flex items-center"><i class="fas fa-check-circle text-primary-600 dark:text-primary-400 mr-2"></i>MySQL</li>
                <li class="flex items-center"><i class="fas fa-check-circle text-primary-600 dark:text-primary-400 mr-2"></i>PostgreSQL</li>
              </ul>
            </article>

            <article
              class="skill-card bg-white/80 dark:bg-gray-900/80 p-6 rounded-2xl shadow-lg hover:shadow-2xl transition-all transform hover:-translate-y-1 border border-gray-200 dark:border-gray-800"
            >
              <div class="text-3xl mb-4 text-primary-600 dark:text-primary-400">
                <i class="fas fa-code"></i>
              </div>
              <h3 class="text-xl font-bold mb-4 text-gray-900 dark:text-white">
                Frontend
              </h3>
              <ul class="space-y-2 text-gray-700 dark:text-gray-300">
                <li class="flex items-center"><i class="fas fa-check-circle text-primary-600 dark:text-primary-400 mr-2"></i>HTML5</li>
                <li class="flex items-center"><i class="fas fa-check-circle text-primary-600 dark:text-primary-400 mr-2"></i>CSS3</li>
                <li class="flex items-center"><i class="fas fa-check-circle text-primary-600 dark:text-primary-400 mr-2"></i>JavaScript</li>
                <li class="flex items-center"><i class="fas fa-check-circle text-primary-600 dark:text-primary-400 mr-2"></i>TailwindCSS</li>
                <li class="flex items-center"><i class="fas fa-check-circle text-primary-600 dark:text-primary-400 mr-2"></i>Responsive Design</li>
              </ul>
            </article>

            <article
              class="skill-card bg-white/80 dark:bg-gray-900/80 p-6 rounded-2xl shadow-lg hover:shadow-2xl transition-all transform hover:-translate-y-1 border border-gray-200 dark:border-gray-800"
            >
              <div class="text-3xl mb-4 text-primary-600 dark:text-primary-400">
                <i class="fas fa-shopping-cart"></i>
              </div>
              <h3 class="text-xl font-bold mb-4 text-gray-900 dark:text-white">
                E-commerce
              </h3>
              <ul class="space-y-2 text-gray-700 dark:text-gray-300">
                <li class="flex items-center"><i class="fas fa-check-circle text-primary-600 dark:text-primary-400 mr-2"></i>Shopify</li>
                <li class="flex items-center"><i class="fas fa-check-circle text-primary-600 dark:text-primary-400 mr-2"></i>Shopify Apps</li>
                <li class="flex items-center"><i class="fas fa-check-circle text-primary-600 dark:text-primary-400 mr-2"></i>Liquid Templates</li>
                <li class="flex items-center"><i class="fas fa-check-circle text-primary-600 dark:text-primary-400 mr-2"></i>Store Customization</li>
              </ul>
            </article>

            <article
              class="skill-card bg-white/80 dark:bg-gray-900/80 p-6 rounded-2xl shadow-lg hover:shadow-2xl transition-all transform hover:-translate-y-1 border border-gray-200 dark:border-gray-800"
            >
              <div class="text-3xl mb-4 text-primary-600 dark:text-primary-400">
                <i class="fas fa-tools"></i>
              </div>
              <h3 class="text-xl font-bold mb-4 text-gray-900 dark:text-white">
                Tools & DevOps
              </h3>
              <ul class="space-y-2 text-gray-700 dark:text-gray-300">
                <li class="flex items-center"><i class="fas fa-check-circle text-primary-600 dark:text-primary-400 mr-2"></i>Git & GitHub</li>
                <li class="flex items-center"><i class="fas fa-check-circle text-primary-600 dark:text-primary-400 mr-2"></i>Docker</li>
                <li class="flex items-center"><i class="fas fa-check-circle text-primary-600 dark:text-primary-400 mr-2"></i>CI/CD</li>
                <li class="flex items-center"><i class="fas fa-check-circle text-primary-600 dark:text-primary-400 mr-2"></i>Linux</li>
                <li class="flex items-center"><i class="fas fa-check-circle text-primary-600 dark:text-primary-400 mr-2"></i>AWS</li>
              </ul>
            </article>
          </div>
        </div>
      </section>

      <section
        id="experience"
        class="py-20 sm:py-24 px-4 sm:px-6 lg:px-8 bg-gray-50/80 dark:bg-gray-900/70"
      >
        <div class="max-w-5xl mx-auto">
          <div class="text-center mb-12">
            <p
              class="text-sm font-semibold uppercase tracking-wider text-primary-700 dark:text-primary-300 mb-3"
            >
              Professional Experience
            </p>
            <h2 class="text-4xl md:text-5xl font-black text-gray-900 dark:text-white">
              Career timeline
            </h2>
          </div>

          <div class="relative">
            <div
              class="absolute left-4 md:left-1/2 top-0 bottom-0 w-0.5 bg-primary-300 dark:bg-primary-700 transform md:-translate-x-1/2"
            ></div>

            <div class="space-y-8">
              <article class="relative flex items-start">
                <span
                  class="absolute left-2 md:left-1/2 w-4 h-4 rounded-full bg-primary-600 transform md:-translate-x-1/2 mt-8 ring-4 ring-white dark:ring-gray-900"
                ></span>
                <div class="ml-12 md:ml-0 md:w-1/2 md:pr-10">
                  <div
                    class="bg-white dark:bg-gray-900 rounded-2xl p-6 border border-gray-200 dark:border-gray-800 shadow-lg"
                  >
                    <div class="flex items-center justify-between mb-2 gap-3">
                      <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                        Software Engineer
                      </h3>
                      <span class="text-sm font-semibold text-primary-600 dark:text-primary-400"
                        >Present</span
                      >
                    </div>
                    <p class="text-primary-600 dark:text-primary-400 font-semibold mb-3">
                      <a
                        href="https://altruan.de"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="hover:underline"
                        >Altruan GMBH</a
                      >
                    </p>
                    <p class="text-gray-700 dark:text-gray-300 leading-relaxed">
                      Developing and maintaining scalable web applications using
                      Laravel and PHP. Building custom Shopify solutions and
                      RESTful APIs. Collaborating with cross-functional teams to
                      deliver high-quality software solutions.
                    </p>
                  </div>
                </div>
              </article>

              <article class="relative flex items-start md:flex-row-reverse">
                <span
                  class="absolute left-2 md:left-1/2 w-4 h-4 rounded-full bg-primary-600 transform md:-translate-x-1/2 mt-8 ring-4 ring-white dark:ring-gray-900"
                ></span>
                <div class="ml-12 md:ml-0 md:w-1/2 md:pl-10">
                  <div
                    class="bg-white dark:bg-gray-900 rounded-2xl p-6 border border-gray-200 dark:border-gray-800 shadow-lg"
                  >
                    <div class="flex items-center justify-between mb-2 gap-3">
                      <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                        Full Stack Developer
                      </h3>
                      <span class="text-sm font-semibold text-primary-600 dark:text-primary-400"
                        >2020 - 2025</span
                      >
                    </div>
                    <p class="text-primary-600 dark:text-primary-400 font-semibold mb-3">
                      <a
                        href="https://technoplusit.com.au"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="hover:underline"
                        >technoPLUS IT</a
                      >
                    </p>
                    <p class="text-gray-700 dark:text-gray-300 leading-relaxed">
                      Built responsive web applications from concept to
                      deployment. Implemented frontend interfaces with modern
                      CSS and JavaScript, and developed robust backend systems
                      with Laravel framework.
                    </p>
                  </div>
                </div>
              </article>

              <article class="relative flex items-start">
                <span
                  class="absolute left-2 md:left-1/2 w-4 h-4 rounded-full bg-primary-600 transform md:-translate-x-1/2 mt-8 ring-4 ring-white dark:ring-gray-900"
                ></span>
                <div class="ml-12 md:ml-0 md:w-1/2 md:pr-10">
                  <div
                    class="bg-white dark:bg-gray-900 rounded-2xl p-6 border border-gray-200 dark:border-gray-800 shadow-lg"
                  >
                    <div class="flex items-center justify-between mb-2 gap-3">
                      <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                        Junior Developer
                      </h3>
                      <span class="text-sm font-semibold text-primary-600 dark:text-primary-400"
                        >2018 - 2020</span
                      >
                    </div>
                    <p class="text-primary-600 dark:text-primary-400 font-semibold mb-3">
                      <a
                        href="https://www.creativeitem.com"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="hover:underline"
                        >Creativeitem</a
                      >
                    </p>
                    <p class="text-gray-700 dark:text-gray-300 leading-relaxed">
                      Started my professional journey working on PHP-based
                      projects. Gained expertise in database design, API
                      development, and modern web development practices.
                      Contributed to building high-quality web applications and
                      digital products.
                    </p>
                  </div>
                </div>
              </article>
            </div>
          </div>
        </div>
      </section>

      <section id="projects" class="py-20 sm:py-24 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
          <div class="text-center mb-12">
            <p
              class="text-sm font-semibold uppercase tracking-wider text-primary-700 dark:text-primary-300 mb-3"
            >
              Featured Projects
            </p>
            <h2 class="text-4xl md:text-5xl font-black text-gray-900 dark:text-white">
              Work highlights
            </h2>
          </div>

          <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-7">
            <article
              class="project-card group bg-white/80 dark:bg-gray-900/80 rounded-2xl shadow-lg hover:shadow-2xl transition-all transform hover:-translate-y-1 border border-gray-200 dark:border-gray-800 overflow-hidden"
            >
              <div
                class="h-44 bg-gradient-to-br from-primary-400 via-primary-500 to-primary-700 flex items-center justify-center"
              >
                <i class="fas fa-shopping-bag text-5xl text-white/90"></i>
              </div>
              <div class="p-6">
                <h3 class="text-xl font-bold mb-2 text-gray-900 dark:text-white">
                  E-commerce Platform
                </h3>
                <p class="text-gray-700 dark:text-gray-300 mb-4 leading-relaxed">
                  A full-featured e-commerce solution built with Laravel and
                  Shopify integration. Includes custom storefront, payment
                  processing, and inventory management.
                </p>
                <div class="flex flex-wrap gap-2">
                  <span class="px-3 py-1 bg-primary-100 dark:bg-primary-900 text-primary-700 dark:text-primary-300 rounded-full text-sm">Laravel</span>
                  <span class="px-3 py-1 bg-primary-100 dark:bg-primary-900 text-primary-700 dark:text-primary-300 rounded-full text-sm">Shopify</span>
                  <span class="px-3 py-1 bg-primary-100 dark:bg-primary-900 text-primary-700 dark:text-primary-300 rounded-full text-sm">PHP</span>
                </div>
              </div>
            </article>

            <article
              class="project-card group bg-white/80 dark:bg-gray-900/80 rounded-2xl shadow-lg hover:shadow-2xl transition-all transform hover:-translate-y-1 border border-gray-200 dark:border-gray-800 overflow-hidden"
            >
              <div
                class="h-44 bg-gradient-to-br from-primary-500 to-primary-800 flex items-center justify-center"
              >
                <i class="fab fa-shopify text-5xl text-white/90"></i>
              </div>
              <div class="p-6">
                <h3 class="text-xl font-bold mb-2 text-gray-900 dark:text-white">
                  Shopify App
                </h3>
                <p class="text-gray-700 dark:text-gray-300 mb-4 leading-relaxed">
                  Custom Shopify application built with Laravel and Shopify API
                  integration. Features include product management, order
                  processing, and automated workflows for e-commerce stores.
                </p>
                <div class="flex flex-wrap gap-2">
                  <span class="px-3 py-1 bg-primary-100 dark:bg-primary-900 text-primary-700 dark:text-primary-300 rounded-full text-sm">Laravel</span>
                  <span class="px-3 py-1 bg-primary-100 dark:bg-primary-900 text-primary-700 dark:text-primary-300 rounded-full text-sm">Shopify API</span>
                  <span class="px-3 py-1 bg-primary-100 dark:bg-primary-900 text-primary-700 dark:text-primary-300 rounded-full text-sm">PHP</span>
                  <span class="px-3 py-1 bg-primary-100 dark:bg-primary-900 text-primary-700 dark:text-primary-300 rounded-full text-sm">REST API</span>
                </div>
              </div>
            </article>

            <article
              class="project-card group bg-white/80 dark:bg-gray-900/80 rounded-2xl shadow-lg hover:shadow-2xl transition-all transform hover:-translate-y-1 border border-gray-200 dark:border-gray-800 overflow-hidden"
            >
              <div
                class="h-44 bg-gradient-to-br from-primary-600 to-primary-900 flex items-center justify-center"
              >
                <i class="fas fa-chart-pie text-5xl text-white/90"></i>
              </div>
              <div class="p-6">
                <h3 class="text-xl font-bold mb-2 text-gray-900 dark:text-white">
                  Financial App
                </h3>
                <p class="text-gray-700 dark:text-gray-300 mb-4 leading-relaxed">
                  Comprehensive financial management application built with
                  Laravel. Features include transaction tracking, budget
                  management, financial reporting, and secure payment
                  processing.
                </p>
                <div class="flex flex-wrap gap-2">
                  <span class="px-3 py-1 bg-primary-100 dark:bg-primary-900 text-primary-700 dark:text-primary-300 rounded-full text-sm">Laravel</span>
                  <span class="px-3 py-1 bg-primary-100 dark:bg-primary-900 text-primary-700 dark:text-primary-300 rounded-full text-sm">PHP</span>
                  <span class="px-3 py-1 bg-primary-100 dark:bg-primary-900 text-primary-700 dark:text-primary-300 rounded-full text-sm">MySQL</span>
                  <span class="px-3 py-1 bg-primary-100 dark:bg-primary-900 text-primary-700 dark:text-primary-300 rounded-full text-sm">Payment Gateway</span>
                </div>
              </div>
            </article>
          </div>
        </div>
      </section>

      <section
        id="contact"
        class="py-20 sm:py-24 px-4 sm:px-6 lg:px-8 bg-gray-50/80 dark:bg-gray-900/70"
      >
        <div class="max-w-6xl mx-auto">
          <div
            class="rounded-3xl bg-gradient-to-br from-white/90 to-primary-50/70 dark:from-gray-900/90 dark:to-gray-900 border border-gray-200 dark:border-gray-800 p-8 sm:p-10 lg:p-12 shadow-xl"
          >
            <div class="max-w-3xl mb-10">
              <p
                class="text-sm font-semibold uppercase tracking-wider text-primary-700 dark:text-primary-300 mb-3"
              >
                Get In Touch
              </p>
              <h2 class="text-4xl md:text-5xl font-black text-gray-900 dark:text-white mb-4">
                Let's build something meaningful
              </h2>
              <p class="text-lg text-gray-700 dark:text-gray-300 leading-relaxed">
                I'm always open to discussing new projects, creative ideas, or
                opportunities to be part of your vision. Feel free to reach out!
              </p>
            </div>

            <div class="grid md:grid-cols-3 gap-5">
              <a
                href="mailto:contact@mdtanvir.com"
                class="contact-card bg-white dark:bg-gray-900 p-6 rounded-2xl shadow-lg hover:shadow-2xl transition-all transform hover:-translate-y-1 border border-gray-200 dark:border-gray-800 text-center"
              >
                <div class="text-4xl mb-4 text-primary-600 dark:text-primary-400">
                  <i class="fas fa-envelope"></i>
                </div>
                <h3 class="font-bold mb-2 text-gray-900 dark:text-white">Email</h3>
                <p class="text-gray-700 dark:text-gray-300 text-sm">
                  contact@mdtanvir.com
                </p>
              </a>

              <a
                href="https://www.linkedin.com/in/tanvir-cs"
                target="_blank"
                rel="noopener noreferrer"
                class="contact-card bg-white dark:bg-gray-900 p-6 rounded-2xl shadow-lg hover:shadow-2xl transition-all transform hover:-translate-y-1 border border-gray-200 dark:border-gray-800 text-center"
              >
                <div class="text-4xl mb-4 text-primary-600 dark:text-primary-400">
                  <i class="fab fa-linkedin"></i>
                </div>
                <h3 class="font-bold mb-2 text-gray-900 dark:text-white">
                  LinkedIn
                </h3>
                <p class="text-gray-700 dark:text-gray-300 text-sm">
                  Connect with me
                </p>
              </a>

              <a
                href="https://github.com/tanvir-cs"
                target="_blank"
                rel="noopener noreferrer"
                class="contact-card bg-white dark:bg-gray-900 p-6 rounded-2xl shadow-lg hover:shadow-2xl transition-all transform hover:-translate-y-1 border border-gray-200 dark:border-gray-800 text-center"
              >
                <div class="text-4xl mb-4 text-primary-600 dark:text-primary-400">
                  <i class="fab fa-github"></i>
                </div>
                <h3 class="font-bold mb-2 text-gray-900 dark:text-white">GitHub</h3>
                <p class="text-gray-700 dark:text-gray-300 text-sm">View my code</p>
              </a>
            </div>

            <div
              class="mt-10 pt-6 border-t border-gray-200 dark:border-gray-800 text-center"
            >
              <p class="text-gray-600 dark:text-gray-400">
                © 2026 MD Tanvir Hossain. All rights reserved.
              </p>
            </div>
          </div>
        </div>
      </section>
    </main>

    <button
      id="scroll-top"
      aria-label="Scroll to top"
      class="fixed bottom-8 right-8 p-4 bg-primary-600 hover:bg-primary-700 text-white rounded-full shadow-lg hover:shadow-xl transition-all transform hover:scale-110 opacity-0 pointer-events-none z-50"
    >
      <i class="fas fa-arrow-up"></i>
    </button>
@endsection
