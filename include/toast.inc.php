<div class="position-fixed top-0 start-50 translate-middle-x pt-3" style="z-index: 1001">
    <div id="toast" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                <?php if(isset($_SESSION['toast'])): ?><?= $_SESSION['toast'] ?><?php endif; ?>
            </div>
            <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<?php if(isset($_SESSION['toast'])): ?>
    <script>
        $(document).ready(function(){
            $("#toast").toast('show');
        });
    </script>
<?php endif; ?>

<?php unset($_SESSION['toast']); ?>