<script>
// Shared admin user actions
function _adminHandleAjaxAction(url, successMsgFallback) {
  fetch(url, {
    method: 'POST',
    headers: { 'X-Requested-With': 'XMLHttpRequest' }
  })
    .then(function (res) { return res.json(); })
    .then(function (data) {
      if (data && data.success) {
        // prefer server message if present
        alert(data.message || successMsgFallback || 'Thành công');
        window.location.reload();
      } else {
        alert('Lỗi: ' + (data && data.message ? data.message : 'Không thành công'));
      }
    })
    .catch(function (err) {
      console.error('AJAX error', err);
      alert('Lỗi mạng: ' + err);
    });
}

function resetPassword(userId) {
  if (!confirm('Bạn có chắc muốn reset mật khẩu cho user này?')) return;
  _adminHandleAjaxAction('<?= base_url('admin/user_reset_password/') ?>' + userId, 'Mật khẩu đã được reset.');
}

function toggleLock(userId, isActive) {
  var msg = isActive ? 'Bạn có chắc muốn khóa tài khoản này?' : 'Bạn có chắc muốn mở khóa tài khoản này?';
  if (!confirm(msg)) return;
  _adminHandleAjaxAction('<?= base_url('admin/user_lock/') ?>' + userId, isActive ? 'Tài khoản đã bị khóa.' : 'Tài khoản đã được mở khóa.');
}
</script>
