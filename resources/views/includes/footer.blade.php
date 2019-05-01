<footer id="footer" class="col d-flex flex-column justify-content-end align-items-center pb-3">
    <div class="copyright">© 2017 <b>Chitti</b> Productions. All Rights Reserved.</div>

    <!-- Scripts -->
    <script src="{{ asset('js/manifest.js') }}" defer></script>
    <script src="{{ asset('js/vendor.js') }}" defer></script>
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="{{ asset('js/generic.js') }}" defer></script>
    <script src="{{ asset('js/common.js') }}" defer></script>
    <script src="{{ asset('js/specific.js') }}" defer></script>
    <script src="{{ asset('js/plugins.js') }}" defer></script>
    @stack('pagespecificscripts')
</footer>
