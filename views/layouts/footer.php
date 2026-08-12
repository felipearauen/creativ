<?php
$extraJs = $extraJs ?? [];
$inlineData = $inlineData ?? '';
?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php if (!empty($inlineData)): ?>
<script>
<?php echo $inlineData; ?>
</script>
<?php endif; ?>
<?php foreach ($extraJs as $jsFile): ?>
<script src="assets/js/<?php echo e($jsFile); ?>"></script>
<?php endforeach; ?>
</body>
</html>
