{{-- Blocking FOUC-free Theme Initializer — Include in <head> before @vite --}}
<script>
(function () {
    try {
        var pref = localStorage.getItem('vl-theme');
        var isDark = pref === 'light' ? false : true;
        document.documentElement.classList.toggle('dark', isDark);
        document.documentElement.style.colorScheme = isDark ? 'dark' : 'light';
        var meta = document.querySelector('meta[name="theme-color"]');
        if (meta) meta.setAttribute('content', isDark ? '#05080F' : '#F4F7FB');
    } catch (e) {
        document.documentElement.classList.add('dark');
    }
})();
</script>
