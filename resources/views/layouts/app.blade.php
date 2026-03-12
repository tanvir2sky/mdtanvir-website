<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
  <head>
<meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta
      name="description"
      content="MD Tanvir Hossain - Software Engineer specializing in Laravel, PHP, Shopify, and modern web development"
    />
    <meta
      name="keywords"
      content="MD Tanvir Hossain, Software Engineer, Laravel, PHP, Shopify, Web Developer"
    />
    <meta name="author" content="MD Tanvir Hossain" />
    <title>MD Tanvir Hossain | Software Engineer</title>

    <!-- Google tag (gtag.js) -->
    <script
      async
      src="https://www.googletagmanager.com/gtag/js?id=G-HL9XG1EPX7"
    ></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag() {
        dataLayer.push(arguments);
      }
      gtag("js", new Date());
      gtag("config", "G-HL9XG1EPX7");
    </script>

    @vite(["resources/css/app.css", "resources/js/app.js"])
  </head>
  <body class="bg-white dark:bg-gray-950 text-gray-900 dark:text-gray-100 transition-colors duration-300 antialiased">
    @yield('content')
  </body>
</html>
