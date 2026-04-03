<?php
$checkout_url = $checkout_url ?? '';
$fields = $fields ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Redirecting to PayHere…</title>
  <style>
    body { font-family: system-ui, sans-serif; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; background: #f0f8f0; color: #2c5530; }
    p { text-align: center; }
  </style>
</head>
<body>
  <p>Redirecting to secure PayHere checkout…</p>
  <form id="payhere-form" method="post" action="<?php echo htmlspecialchars($checkout_url, ENT_QUOTES, 'UTF-8'); ?>">
    <?php foreach ($fields as $name => $value): ?>
    <input type="hidden" name="<?php echo htmlspecialchars((string) $name, ENT_QUOTES, 'UTF-8'); ?>" value="<?php echo htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); ?>">
    <?php endforeach; ?>
  </form>
  <script>document.getElementById('payhere-form').submit();</script>
</body>
</html>
