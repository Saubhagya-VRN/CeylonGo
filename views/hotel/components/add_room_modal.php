<style>
	.room-modal-overlay {
		position: fixed;
		inset: 0;
		display: none;
		align-items: center;
		justify-content: center;
		padding: 24px;
		background: rgba(18, 34, 20, 0.58);
		backdrop-filter: blur(4px);
		z-index: 2000;
		overflow-y: auto;
	}

	.room-modal-overlay.is-open {
		display: flex;
	}

	.room-modal-open {
		overflow: hidden;
	}

	.room-modal {
		width: min(1080px, 100%);
		background: #fff;
		border-radius: 20px;
		box-shadow: 0 24px 70px rgba(0, 0, 0, 0.28);
		padding: 22px;
	}

	.room-modal.room-modal-small {
		width: min(680px, 100%);
	}

	.room-modal-header {
		display: flex;
		align-items: flex-start;
		justify-content: space-between;
		gap: 16px;
		margin-bottom: 18px;
	}

	.room-modal-kicker {
		margin: 0 0 6px;
		color: #2c5530;
		font-size: 12px;
		font-weight: 700;
		letter-spacing: 0.08em;
		text-transform: uppercase;
	}

	.room-modal-header h2 {
		margin: 0;
		color: #214025;
		font-size: 24px;
	}

	.room-modal-close {
		width: 42px;
		height: 42px;
		border: 0;
		border-radius: 999px;
		background: #edf3ee;
		color: #2c5530;
		font-size: 28px;
		line-height: 1;
		cursor: pointer;
	}

	.room-modal-grid {
		display: grid;
		grid-template-columns: repeat(2, minmax(0, 1fr));
		gap: 18px;
	}

	.room-modal-grid.room-modal-grid-tight {
		gap: 14px;
	}

	.room-modal-grid .span-2 {
		grid-column: 1 / -1;
	}

	.input-prefix {
		position: relative;
	}

	.input-prefix span {
		position: absolute;
		left: 14px;
		top: 50%;
		transform: translateY(-50%);
		color: #5f6f5f;
		font-weight: 600;
		pointer-events: none;
	}

	.input-prefix input {
		padding-left: 48px !important;
	}

	.room-helper {
		margin-top: 6px;
		color: #6c7d6d;
		font-size: 12px;
	}

	.room-modal-form .form-group textarea,
	.room-modal-form .form-group input,
	.room-modal-form .form-group select {
		width: 100%;
	}

	.room-modal-actions {
		display: flex;
		justify-content: flex-end;
		gap: 12px;
		margin-top: 20px;
	}

	@media (max-width: 768px) {
		.room-modal {
			padding: 18px;
		}

		.room-modal.room-modal-small {
			width: min(100%, 680px);
		}

		.room-modal-grid {
			grid-template-columns: 1fr;
		}

		.room-modal-actions {
			flex-direction: column-reverse;
		}

		.room-modal-actions .btn {
			width: 100%;
		}
	}
</style>

<div class="room-modal-overlay" id="addRoomModal" aria-hidden="true">
	<div class="room-modal room-modal-small" role="dialog" aria-modal="true" aria-labelledby="addRoomModalTitle">
		<div class="room-modal-header">
			<div>
				<p class="room-modal-kicker">Room Setup</p>
				<h2 id="addRoomModalTitle">Add a New Room</h2>
			</div>
			<button type="button" class="room-modal-close" data-room-modal-close aria-label="Close modal">&times;</button>
		</div>

		<form action="/CeylonGo/public/hotel/rooms" method="POST" class="room-modal-form" id="addRoomForm">
			<input type="hidden" id="room_id" name="room_id" value="">
			<div class="room-modal-grid room-modal-grid-tight">
				<div class="form-group">
					<label for="room_number">Room Number</label>
					<input type="text" id="room_number" name="room_number" class="form-control" placeholder="101" required maxlength="50">
				</div>

				<div class="form-group">
					<label for="room_type">Room Type</label>
					<select id="room_type" name="room_type" class="form-control" required>
						<option value="">Select Room Type</option>
						<option value="Standard Room">Standard Room</option>
						<option value="Deluxe Room">Deluxe Room</option>
						<option value="Suite">Suite</option>
						<option value="Family Room">Family Room</option>
						<option value="Executive Room">Executive Room</option>
						<option value="Presidential Suite">Presidential Suite</option>
					</select>
					<div class="room-helper">Choose the room type from the available options.</div>
				</div>

				<div class="form-group input-prefix">
					<label for="rate">Price Per Night</label>
					<span style="margin-top: 15px;">LKR</span>
					<input type="number" id="rate" name="rate" class="form-control" placeholder="12500" min="0" step="1" required>
				</div>

				<div class="form-group">
					<label for="capacity">Capacity</label>
					<input type="number" id="capacity" name="capacity" class="form-control" value="2" min="1" required>
				</div>

				<div class="form-group span-2">
					<label for="description">Description</label>
					<textarea id="description" name="description" class="form-control" rows="3" placeholder="Cozy double room with garden view, ideal for couples."></textarea>
				</div>

				<input type="hidden" name="status" value="available">
			</div>

			<div class="room-modal-actions">
				<button type="button" class="btn btn-secondary" data-room-modal-close>Cancel</button>
				<button type="submit" class="btn btn-primary" id="submitBtn">Save Room</button>
			</div>
		</form>
	</div>
</div>

<script>
	document.addEventListener('DOMContentLoaded', function() {
		const modal = document.getElementById('addRoomModal');
		const modalTitle = document.getElementById('addRoomModalTitle');
		const submitBtn = document.getElementById('submitBtn');
		const form = document.getElementById('addRoomForm');
		const roomIdInput = document.getElementById('room_id');

		// Open modal for adding new room
		document.querySelectorAll('[data-room-modal-open]').forEach(btn => {
			btn.addEventListener('click', function() {
				modalTitle.textContent = 'Add a New Room';
				submitBtn.textContent = 'Save Room';
				roomIdInput.value = '';
				form.reset();
				form.method = 'POST';
				form.action = '/CeylonGo/public/hotel/rooms';
				modal.classList.add('is-open');
				document.body.classList.add('room-modal-open');
			});
		});

		// Open modal for editing room
		document.querySelectorAll('[data-room-modal-edit]').forEach(btn => {
			btn.addEventListener('click', function() {
				const roomId = this.dataset.roomId;
				const roomNumber = this.dataset.roomNumber;
				const roomType = this.dataset.roomType;
				const price = this.dataset.price;
				const capacity = this.dataset.capacity;
				const description = this.dataset.description;

				// Populate form fields
				document.getElementById('room_number').value = roomNumber || '';
				document.getElementById('room_type').value = roomType || '';
				document.getElementById('rate').value = price || '';
				document.getElementById('capacity').value = capacity || '2';
				document.getElementById('description').value = description || '';
				roomIdInput.value = roomId || '';

				// Update modal title and button
				modalTitle.textContent = 'Edit Room';
				submitBtn.textContent = 'Update Room';

				// Change form to POST method for updates
				form.method = 'POST';
				form.action = '/CeylonGo/public/hotel/rooms';

				// Open modal
				modal.classList.add('is-open');
				document.body.classList.add('room-modal-open');
			});
		});

		// Close modal
		document.querySelectorAll('[data-room-modal-close]').forEach(btn => {
			btn.addEventListener('click', function() {
				modal.classList.remove('is-open');
				document.body.classList.remove('room-modal-open');
			});
		});

		// Close modal when clicking overlay
		modal.addEventListener('click', function(e) {
			if (e.target === modal) {
				modal.classList.remove('is-open');
				document.body.classList.remove('room-modal-open');
			}
		});

		// Dismiss notice
		document.querySelectorAll('[data-dismiss-notice]').forEach(btn => {
			btn.addEventListener('click', function() {
				this.closest('[role="status"], [role="alert"]').style.display = 'none';
			});
		});
	});
</script>
