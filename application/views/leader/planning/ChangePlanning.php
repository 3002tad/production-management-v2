<?php
// View: Form tạo Kế hoạch sản xuất (BGĐ)
// Biến truyền vào (từ controller):
// - $order_options_html: chuỗi <option> cho select đơn hàng (mỗi option có thể đặt data-qty, data-delivery)
// - $machine_options_html: chuỗi <option> cho select dây chuyền (mỗi option có thể đặt data-capacity)
// - $materials: mảng đối tượng nguyên vật liệu (sử dụng để render bảng NVL)
// - $selected_project_id: (tùy chọn) id đơn hàng để auto-select
// Mục đích: hiển thị form tạo kế hoạch sản xuất; form gửi POST về `Leader/storePlan`.
?>
<div class="row">
	<div class="col-12">
		<div class="card my-4">
			<div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
				<div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
					<div class="row px-3">
						<div class="col-8 d-flex align-items-center">
							<i class="material-icons text-white opacity-10 me-2">playlist_add</i>
							<h6 class="text-white mb-0">Điều chỉnh kế hoạch sản xuất</h6>
						</div>
						<div class="col-4 text-end">
							<a href="<?= site_url('leader/planning'); ?>" class="btn bg-gradient-light mb-0">Quay lại</a>
						</div>
					</div>
				</div>
			</div>

			<div class="card-body px-4 pb-4">
				<form method="post" action="<?= isset($plan) ? site_url('Leader/updatePlan') : site_url('Leader/storePlan'); ?>">
					<?php if (isset($plan) && !empty($plan->id_plan)): ?>
						<input type="hidden" name="id_plan" value="<?= htmlspecialchars($plan->id_plan, ENT_QUOTES); ?>" />
					<?php endif; ?>
					<div class="row">
						<div class="col-md-6">
							<div class="card shadow-sm mb-3">
								<div class="card-body">
							   <!-- Chọn đơn hàng: mỗi <option> trong $order_options_html nên chứa
								   data-qty (số lượng đơn) và có thể data-delivery (ngày giao) để JS auto-fill -->
							   <div class="mb-3">
								<div class="card mb-2">
									<div class="card-body p-2">
										<label class="form-label">Chọn đơn hàng (đã duyệt)</label>
										<select id="id_project" name="id_project" class="form-select" required>
											<option value="">-- Chọn đơn hàng --</option>
											<?= $order_options_html ?? ''; ?>
										</select>
									</div>
								</div>
							</div>

							<!-- Số lượng mục tiêu: người dùng có thể chỉnh, hoặc JS sẽ auto-fill từ option.data-qty -->
							<div class="mb-3">
								<div class="card mb-2">
									<div class="card-body p-2">
										<label class="form-label">Tên kế hoạch (tuỳ chọn)</label>
										<input type="text" name="plan_name" id="plan_name" class="form-control" maxlength="255" placeholder="Tên kế hoạch" value="<?= isset($plan) ? htmlspecialchars($plan->plan_name ?? '', ENT_QUOTES) : ''; ?>" />
									</div>
								</div>
							</div>

							<div class="mb-3">
								<div class="card mb-2">
									<div class="card-body p-2">
										<label class="form-label">Số lượng mục tiêu</label>
										<input type="number" step="1" name="qty_target" id="qty_target" class="form-control" required value="<?= isset($plan) ? htmlspecialchars($plan->qty_target ?? '', ENT_QUOTES) : ''; ?>" />
									</div>
								</div>
							</div>

							<!--
								Trường ngày (ghi chú):
								- end_date: Hạn giao (có thể được auto-fill từ option.data-delivery của đơn hàng)
								- start_date: Ngày bắt đầu (tùy chọn)
								- finish_date: Ngày kết thúc (tùy chọn)
								Kiểm tra phía client: start/finish không được sau end_date; kiểm tra thêm trên server là cần thiết.
							-->
							   <!-- Chọn dây chuyền: option trong $machine_options_html nên chứa data-capacity (đơn vị: sản phẩm/giờ hoặc tương tự)
								   JS sẽ dùng data-capacity để tính "số ca đề xuất" -->
							   <div class="mb-3">
							
							</div>

							<!-- Số ca đề xuất: JS tính dựa trên qty_target và data-capacity của máy; giả định 8 giờ/ca -->
							<div class="mb-3">
								<div class="card mb-2">
									<div class="card-body p-2">
										<label class="form-label">Ngày bắt đầu (tùy chọn)</label>
										<input type="date" name="start_date" id="start_date" class="form-control" value="<?= isset($plan) && !empty($plan->start_date) ? htmlspecialchars(date('Y-m-d', strtotime($plan->start_date)), ENT_QUOTES) : ''; ?>" />
									</div>
								</div>
							</div>

							<div class="mb-3">
								<div class="card mb-2">
									<div class="card-body p-2">
										<label class="form-label">Ngày kết thúc (tùy chọn)</label>
										<input type="date" name="finish_date" id="finish_date" class="form-control" value="<?= isset($plan) && !empty($plan->finish_date) ? htmlspecialchars(date('Y-m-d', strtotime($plan->finish_date)), ENT_QUOTES) : (isset($plan) && !empty($plan->end_date) ? htmlspecialchars(date('Y-m-d', strtotime($plan->end_date)), ENT_QUOTES) : ''); ?>" />
									</div>
								</div>
							</div>

							<!-- Automatic allocation removed: users cannot auto-create NVL allocations here -->
							</div>
							</div>
						</div>

						<div class="col-md-6">
							<div class="card shadow-sm mb-3">
								<div class="card-body">
							<div class="mb-3">
								<div class="card mb-2">
									<div class="card-body p-2">
										<label class="form-label">Chọn công suất</label>
										<select id="machine_id" name="machine_id" class="form-select">
											<option value="">-- Không chọn --</option>
											<?= $machine_options_html ?? ''; ?>
										</select>
									</div>
								</div>
							</div>

							<div class="mb-3">
								<div class="card mb-2">
									<div class="card-body p-2">
										<label class="form-label">Số ca đề xuất(8 giờ/ca)</label>
										<div class="input-group">
											<input type="number" id="suggested_shifts" name="suggested_shifts" class="form-control" min="0" value="<?= isset($plan) ? htmlspecialchars($plan->suggested_shifts ?? '', ENT_QUOTES) : ''; ?>" />
											<button type="button" id="calc_shifts" class="btn btn-outline-secondary">Tính số ca</button>
										</div>
										<small class="text-muted"></small>
									</div>
								</div>
							</div>

							<div class="mb-3">
								<div class="card mb-2">
									<div class="card-body p-2">
										<label class="form-label">Ghi chú</label>
										<textarea name="note" class="form-control" rows="3"><?=
											(isset($incident_description) && $incident_description !== '')
												? htmlspecialchars($incident_description, ENT_QUOTES)
												: (isset($plan) ? htmlspecialchars($plan->note ?? '', ENT_QUOTES) : '');
										?></textarea>
									</div>
								</div>
							</div>
							</div>
						</div>
					</div>


						<!-- Materials UI removed from ChangePlanning -->

										<!--
												Hidden inputs:
												- `lines`: lưu thông tin dây chuyền/chỉ dẫn (JS cập nhật khi thay đổi `machine_id`)
													Lưu ý: backend có thể mong đợi một mảng JSON; JS hiện lưu 1 chuỗi JSON của label (xem script).
												- `auto_approve`: flag (0/1) do 2 nút submit điều khiển
										-->
										<input type="hidden" name="lines" id="lines" value='<?= isset($plan) ? (is_string($plan->lines) ? htmlspecialchars($plan->lines, ENT_QUOTES) : htmlspecialchars(json_encode($plan->lines ?? [], JSON_UNESCAPED_UNICODE), ENT_QUOTES)) : '[]'; ?>' />
										<input type="hidden" name="auto_approve" id="auto_approve" value="1" />

					<div class="text-end">
						<a href="<?= site_url('leader/planning'); ?>" class="btn btn-secondary">Hủy</a>
						<button type="submit" id="btn_save_approve" class="btn btn-success ms-2">Lưu</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
	// Script chịu trách nhiệm (tóm tắt):
	// - Auto-fill các trường từ option của select (ví dụ: data-qty, data-delivery)
	// - Tính "số ca đề xuất" dựa trên qty_target và data-capacity của máy
	// - Cập nhật cột "Số lượng thiếu" trong bảng NVL theo NVL được chọn
	// - Xây textarea `#materials` chứa danh sách NVL (plain-text)
	// - Gán flag `auto_approve` dựa trên nút submit được bấm
	// - Thực hiện kiểm tra ràng buộc ngày phía client trước khi submit
	// LƯU Ý: mọi kiểm tra phía client là thao tác UX; bắt buộc phải kiểm tra lại trên server.
	const projectSelect = document.getElementById('id_project');
	const qtyInput = document.getElementById('qty_target');
	const endDateInput = document.getElementById('end_date');
	// If server rendered a `$plan`, we are in edit mode — avoid overwriting server value
	const isEditing = <?= isset($plan) ? 'true' : 'false'; ?>;
	const startDateInput = document.getElementById('start_date');
	const finishDateInput = document.getElementById('finish_date');
	const machineSelect = document.getElementById('machine_id');
	const calcBtn = document.getElementById('calc_shifts');
	const suggestedInput = document.getElementById('suggested_shifts');
	const linesInput = document.getElementById('lines');

	// Nếu có project_id trong URL, auto fill qty
	// Lưu ý client-side: phần JS phía dưới sẽ gợi ý ngày bắt đầu/kết thúc và
	// chặn submit nếu vi phạm ràng buộc ngày (start/finish không được sau end_date).
	if (projectSelect) {
		projectSelect.addEventListener('change', function() {
			const opt = projectSelect.options[projectSelect.selectedIndex];
			if (opt && opt.dataset.qty) {
				// only auto-fill qty when not editing an existing plan or when the field is empty
				if (!isEditing || (qtyInput && !qtyInput.value)) {
					qtyInput.value = opt.dataset.qty;
				}
			}
			// Auto-fill end_date from option data-delivery if present
			if (opt && endDateInput && opt.dataset.delivery) {
				// set only when a value exists
				endDateInput.value = opt.dataset.delivery;
			}
			// if end date filled and finish/start empty, set reasonable defaults
			if (endDateInput && endDateInput.value) {
				if (startDateInput && !startDateInput.value) {
					// prefer using order creation date if available and before end date
					const created = (opt && opt.dataset && opt.dataset.created) ? opt.dataset.created : null;
					if (created && (new Date(created) < new Date(endDateInput.value))) {
						startDateInput.value = created;
					} else {
						const today = new Date().toISOString().slice(0,10);
						startDateInput.value = today < endDateInput.value ? today : endDateInput.value;
					}
				}
				if (finishDateInput && !finishDateInput.value) {
					// default finish to one day before end_date
					const ed = new Date(endDateInput.value);
					ed.setDate(ed.getDate() - 1);
					finishDateInput.value = ed.toISOString().slice(0,10);
				}
			}
					// no BOM fetch: ChangePlanning does not auto-apply materials
		});

		// tự chọn nếu có param
			// tự chọn nếu controller cung cấp `selected_project_id`
			<?php if (!empty($selected_project_id)): ?>
			(function() {
					const id = '<?= htmlspecialchars($selected_project_id, ENT_QUOTES); ?>';
				for (let i=0;i<projectSelect.options.length;i++) {
					if (projectSelect.options[i].value == id) { projectSelect.selectedIndex = i; projectSelect.dispatchEvent(new Event('change')); break; }
				}
			})();
			// select saved machine (client-side): prefer machine_id, fallback to matching lines label
			(function(){
				const existingMachineId = '<?= htmlspecialchars($plan->machine_id ?? '', ENT_QUOTES); ?>';
				const existingLines = <?= isset($plan->lines) ? json_encode($plan->lines) : 'null'; ?>;
				setTimeout(function(){
					try {
						if (existingMachineId && machineSelect) {
							for (let j=0;j<machineSelect.options.length;j++) {
								if (machineSelect.options[j].value == existingMachineId) { machineSelect.selectedIndex = j; machineSelect.dispatchEvent(new Event('change')); break; }
							}
						} else if (existingLines && machineSelect) {
							let labelToMatch = null;
							if (Array.isArray(existingLines) && existingLines.length>0) labelToMatch = existingLines[0];
							else if (typeof existingLines === 'string') { try { const parsed = JSON.parse(existingLines); if (Array.isArray(parsed) && parsed.length>0) labelToMatch = parsed[0]; else labelToMatch = existingLines; } catch(e){ labelToMatch = existingLines; } }
							if (labelToMatch) {
								for (let j=0;j<machineSelect.options.length;j++) {
									const txt = (machineSelect.options[j].textContent || machineSelect.options[j].innerText || '').trim();
									if (txt.indexOf(labelToMatch) !== -1) { machineSelect.selectedIndex = j; machineSelect.dispatchEvent(new Event('change')); break; }
						// else continue; we'll try capacity match after loop
								}
							}
						}
					} catch(e){ console && console.warn && console.warn('Select saved machine failed', e); }
				},50);
				// Fallback attempt: if still no machine selected, try matching by capacity number in existingLines
				setTimeout(function(){
					try {
						if (machineSelect && (machineSelect.options[machineSelect.selectedIndex] || {}).value === '' && existingLines) {
							let labelToMatch = null;
							if (Array.isArray(existingLines) && existingLines.length>0) labelToMatch = existingLines[0];
							else if (typeof existingLines === 'string') {
								try { const parsed = JSON.parse(existingLines); if (Array.isArray(parsed) && parsed.length>0) labelToMatch = parsed[0]; else labelToMatch = existingLines; } catch(e){ labelToMatch = existingLines; }
							}
							const capMatch = (labelToMatch || '').match(/(\d+(?:\.\d+)?)/);
							if (capMatch && capMatch[1]) {
								const want = parseFloat(capMatch[1]);
								for (let k=0;k<machineSelect.options.length;k++) {
									const cap = parseFloat(machineSelect.options[k].dataset.capacity || 0);
									if (!isNaN(cap) && Math.abs(cap - want) < 0.01) { machineSelect.selectedIndex = k; machineSelect.dispatchEvent(new Event('change')); break; }
								}
							}
						}
					} catch(e) { console && console.warn && console.warn('Capacity fallback failed', e); }
				}, 120);
			})();
			<?php endif; ?>
	}

	// Biến lưu giá trị số ca do hệ thống tính tự động (dùng để so sánh khi người dùng nhập tay)
	let autoCalculatedShifts = 0;

	// Tính số ca đề xuất dựa trên `qty_target` và `data-capacity` của máy (8 giờ/ca)
	// Ghi chú (tiếng Việt):
	// - `autoCalculatedShifts` lưu lại giá trị tối thiểu hệ thống đề xuất.
	// - Người dùng có thể nhập tay vào `#suggested_shifts`; khi giá trị nhập tay nhỏ hơn
	//   `autoCalculatedShifts` => nghĩa là vượt quá công suất (không đủ giờ để hoàn thành),
	//   sẽ báo lỗi khi submit.
	function computeShifts() {
		const qty = parseFloat(qtyInput.value) || 0;
		const machineOpt = machineSelect ? machineSelect.options[machineSelect.selectedIndex] : null;
		const capacity = machineOpt ? parseFloat(machineOpt.dataset.capacity || 0) : 0;
		const shiftHours = 8; // giả định

		if (!capacity || capacity <= 0) {
			alert('Không có dữ liệu công suất. Vui lòng chọn dây chuyền.');
			autoCalculatedShifts = 0;
			suggestedInput.value = '';
			return;
		}

		const hoursNeeded = qty / capacity;
		const shifts = Math.max(0, Math.ceil(hoursNeeded / shiftHours));
		autoCalculatedShifts = shifts;
		suggestedInput.value = shifts;
	}
	// Cho phép tính khi bấm nút và khi thay đổi input liên quan
	calcBtn.addEventListener('click', computeShifts);
	if (qtyInput) qtyInput.addEventListener('change', computeShifts);
	if (machineSelect) machineSelect.addEventListener('change', computeShifts);

	// Update hidden `lines` input when machine (dây chuyền) selection changes.
	function updateLinesInput() {
		if (!linesInput) return;
		if (!machineSelect) { linesInput.value = '[]'; return; }
		const opt = machineSelect.options[machineSelect.selectedIndex];
		if (!opt || !opt.value) {
			linesInput.value = '[]';
			return;
		}
		// store only the label text (e.g. "Dây chuyền 1") as a JSON string
		const label = (opt.textContent || opt.innerText || '').trim();
		// store as an array to match backend expectations (e.g. ["Dây chuyền 1"])
		linesInput.value = JSON.stringify([label]);
	}
	if (machineSelect) {
		machineSelect.addEventListener('change', updateLinesInput);
	}
	// initialize on load
	updateLinesInput();

	// Materials handling removed: ChangePlanning does not interact with materials

	// Client-side: control auto_approve flag via buttons, and validation
	const autoApproveInput = document.getElementById('auto_approve');
	const btnSave = document.getElementById('btn_save');
	const btnSaveApprove = document.getElementById('btn_save_approve');

	// Ensure default value
	if (autoApproveInput) autoApproveInput.value = '0';

	if (btnSave) {
		btnSave.addEventListener('click', function() {
			if (autoApproveInput) autoApproveInput.value = '0';
		});
	}
	if (btnSaveApprove) {
		btnSaveApprove.addEventListener('click', function() {
			if (autoApproveInput) autoApproveInput.value = '1';
		});
	}

	// Client-side validation: start_date and finish_date must not be later than delivery (end_date)
	const form = document.querySelector('form[method="post"]');
	if (form) {
		form.addEventListener('submit', function(e) {
			e.preventDefault(); // luôn ngăn submit mặc định để chạy kiểm tra async

			const endVal = endDateInput ? endDateInput.value : '';
			const startVal = startDateInput ? startDateInput.value : '';
			const finishVal = finishDateInput ? finishDateInput.value : '';

			function isAfter(a, b) {
				if (!a || !b) return false;
				return new Date(a) > new Date(b);
			}

			// Kiểm tra ngày (client-side) — nếu sai sẽ dừng
			if (endVal) {
				if (isAfter(startVal, endVal)) {
					alert('Ngày bắt đầu không được trễ hơn hạn giao');
					return false;
				}
				// `finish_date` must be strictly before `end_date` (hạn giao)
				if (finishVal && endVal && (new Date(finishVal) >= new Date(endVal))) {
					alert('Ngày kết thúc phải trước hạn giao');
					return false;
				}
			}
			if (startVal && finishVal && isAfter(startVal, finishVal)) {
				alert('Ngày bắt đầu không được sau ngày kết thúc');
				return false;
			}

			// Kiểm tra số ca: nếu người dùng nhập tay số ca nhỏ hơn số ca hệ thống đề xuất => báo lỗi
			// Lưu ý: autoCalculatedShifts được cập nhật khi bấm 'Tính số ca' hoặc khi thay đổi qty/machine
			const userShifts = parseInt((suggestedInput && suggestedInput.value) ? suggestedInput.value : 0) || 0;
			if (autoCalculatedShifts && userShifts < autoCalculatedShifts) {
				// Thông báo tiếng Việt rõ ràng theo yêu cầu
				alert('Số ca nhập vào nhỏ hơn số ca tối thiểu đề xuất — vượt quá công suất. Vui lòng kiểm tra lại.');
				return false;
			}

			// Nếu qua hết kiểm tra, submit form
			form.submit();
			return false;
		});
	}
});
</script>
<?php if (isset($plan)): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
	try {
		// set machine selection if available — try multiple possible property names
		var mval = '';
		try {
			mval = '<?= htmlspecialchars($plan->machine_id ?? $plan->id_machine ?? $plan->machine ?? $plan->machineId ?? '', ENT_QUOTES); ?>';
		} catch (e) { mval = '<?= htmlspecialchars($plan->machine_id ?? '', ENT_QUOTES); ?>'; }
		if (mval) {
			var ms = document.getElementById('machine_id');
			if (ms) {
				for (var i=0;i<ms.options.length;i++) {
					if (String(ms.options[i].value) == String(mval)) {
						ms.selectedIndex = i;
						// trigger change so existing handlers run
						ms.dispatchEvent(new Event('change'));
						// also trigger qty change and calc button in case listeners need it
						try {
							var qtyEl = document.getElementById('qty_target');
							if (qtyEl) qtyEl.dispatchEvent(new Event('input'));
							var calcBtn = document.getElementById('calc_shifts');
							if (calcBtn) calcBtn.click();
						} catch (e) { /* ignore */ }
						break;
					}
				}
			}
		}
		// ensure project is selected (controller also tries to mark it)
		var pval = '<?= htmlspecialchars($plan->id_project ?? '', ENT_QUOTES); ?>';
		if (pval) {
			var ps = document.getElementById('id_project');
			if (ps) {
				for (var j=0;j<ps.options.length;j++) {
					if (ps.options[j].value == pval) {
						ps.selectedIndex = j;
						// dispatch change to trigger other handlers (like BOM fetch),
						// but restore only the plan's finish date into #finish_date so
						// the project's delivery (auto-filled into #end_date) remains.
							ps.dispatchEvent(new Event('change'));
							try {
								var planFinish = '<?= isset($plan->finish_date) && !empty($plan->finish_date) ? htmlspecialchars(date('Y-m-d', strtotime($plan->finish_date)), ENT_QUOTES) : (isset($plan->end_date) && !empty($plan->end_date) ? htmlspecialchars(date('Y-m-d', strtotime($plan->end_date)), ENT_QUOTES) : ''); ?>';
								if (planFinish) {
									var fd = document.getElementById('finish_date');
									if (fd) fd.value = planFinish;
								}
								// restore qty_target from the plan after the project's change handler
								try {
									var planQty = '<?= htmlspecialchars($plan->qty_target ?? '', ENT_QUOTES); ?>';
									if (planQty) {
										var qel = document.getElementById('qty_target');
										if (qel) qel.value = planQty;
									}
								} catch (e) { /* ignore */ }
							} catch (e) { console && console.warn && console.warn('Restore plan finish date failed', e); }
						break;
					}
				}
			}
		}
	} catch (e) { console && console.warn && console.warn('Prefill script error', e); }
});
</script>
<?php endif; ?>

