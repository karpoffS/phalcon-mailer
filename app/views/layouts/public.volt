<div class="navbar navbar-default " role="navigation">
    {{ partial("layouts/menu") }}
</div>

<div class="container main-container">
  {{ content() }}
</div>

<footer class="footer">
  Сделано с любовью командой SimpleStartup
  {{ link_to("privacy", "Политика безопастности") }} {{ link_to("terms", "Договор оферта") }}
  © {{ date("Y") }} SimpleStartup, Inc.
</footer>