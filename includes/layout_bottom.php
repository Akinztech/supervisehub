<?php if (current_user()): ?></div></main><?php else: ?></main><footer class="public-footer"><p>© <?= date('Y') ?> <?= e($config['name']) ?> · <?= e($config['department']) ?>, <?= e($config['institution']) ?></p></footer><?php endif; ?>
<script src="<?= e(url('assets/js/app.js')) ?>"></script>
</body></html>
