</main>
<footer class="container-fluid bg-dark text-light text-center">
    <div class="row">
        <div class="col py-3">
            <?= (defined('BLOGTITLE')) ? BLOGTITLE : 'ANONYMOUS' ?> - &copy; <?= date('Y') ?> - Tous
            droits réservés
        </div>
    </div>
</footer>

<!--bootstrap-->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4"
        crossorigin="anonymous">
</script>
<!--JS-->
<script src="<?= URL; ?>js/blog.js"></script>
</body>
</html>
