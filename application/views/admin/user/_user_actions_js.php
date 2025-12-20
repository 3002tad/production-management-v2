<script>
// Shared admin user actions
function _adminHandleAjaxAction(url, successMsgFallback) {
  fetch(url, {
    method: 'POST',
    headers: { 'X-Requested-With': 'XMLHttpRequest' },
    credentials: 'same-origin'
  })
    .then(function (res) { return res.text(); })
    .then(function (text) {
      var data;
      try {
        data = JSON.parse(text);
      } catch (e) {
        console.error('AJAX parse error, response:', text);
        alert('Lỗi server: ' + (text || 'Không thể phân tích phản hồi JSON'));
        return;
      }

      if (data && data.success) {
        var msg = data.message || successMsgFallback || 'Thành công';
        if (data.temp_password) {
          msg = 'Mật khẩu tạm: ' + data.temp_password + '\n' + msg;
        }
        alert(msg);
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
