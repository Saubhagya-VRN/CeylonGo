<style>
	.booking-modal-overlay {
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

	.booking-modal-overlay.is-open {
		display: flex;
	}

	.booking-modal {
		width: min(800px, 100%);
		background: #fff;
		border-radius: 20px;
		box-shadow: 0 24px 70px rgba(0, 0, 0, 0.28);
		padding: 22px;
		max-height: 90vh;
		overflow-y: auto;
	}

	.booking-modal-header {
		display: flex;
		align-items: flex-start;
		justify-content: space-between;
		gap: 16px;
		margin-bottom: 18px;
		padding-bottom: 16px;
		border-bottom: 1px solid #e0e0e0;
	}

	.booking-modal-kicker {
		margin: 0 0 6px;
		color: #2c5530;
		font-size: 12px;
		font-weight: 700;
		letter-spacing: 0.08em;
		text-transform: uppercase;
	}

	.booking-modal-header h2 {
		margin: 0;
		color: #214025;
		font-size: 24px;
	}

	.booking-modal-close {
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

	.booking-details-grid {
		display: grid;
		grid-template-columns: 1fr 1fr;
		gap: 20px;
		margin-bottom: 24px;
	}

	.booking-detail-item {
		display: flex;
		flex-direction: column;
		gap: 4px;
	}

	.booking-detail-label {
		font-size: 12px;
		font-weight: 600;
		color: #6c7d6d;
		text-transform: uppercase;
		letter-spacing: 0.5px;
	}

	.booking-detail-value {
		font-size: 16px;
		font-weight: 500;
		color: #214025;
	}

	.booking-status {
		padding: 4px 12px;
		border-radius: 20px;
		font-size: 12px;
		font-weight: 600;
		text-transform: uppercase;
		display: inline-block;
	}

	.booking-status.pending {
		background: #fff3cd;
		color: #856404;
	}

	.booking-status.confirmed {
		background: #d4edda;
		color: #155724;
	}

	.booking-status.cancelled {
		background: #f8d7da;
		color: #721c24;
	}

	.booking-modal-actions {
		display: flex;
		justify-content: flex-end;
		gap: 12px;
		margin-top: 24px;
		padding-top: 20px;
		border-top: 1px solid #e0e0e0;
	}

	.btn-accept {
		background: #28a745;
		color: white;
		padding: 0.75rem 1.5rem;
		border: none;
		border-radius: 6px;
		font-weight: 600;
		cursor: pointer;
		transition: all 0.3s ease;
	}

	.btn-accept:hover {
		background: #218838;
		transform: translateY(-1px);
		box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
	}

	.btn-reject {
		background: #dc3545;
		color: white;
		padding: 0.75rem 1.5rem;
		border: none;
		border-radius: 6px;
		font-weight: 600;
		cursor: pointer;
		transition: all 0.3s ease;
	}

	.btn-reject:hover {
		background: #c82333;
		transform: translateY(-1px);
		box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
	}

	.btn-secondary {
		background: #6c757d;
		color: white;
		padding: 0.75rem 1.5rem;
		border: none;
		border-radius: 6px;
		font-weight: 600;
		cursor: pointer;
		transition: all 0.3s ease;
	}

	.btn-secondary:hover {
		background: #5a6268;
		transform: translateY(-1px);
		box-shadow: 0 4px 8px rgba(108, 117, 125, 0.3);
	}

	@media (max-width: 768px) {
		.booking-modal {
			padding: 18px;
			width: min(100%, 800px);
		}

		.booking-details-grid {
			grid-template-columns: 1fr;
			gap: 16px;
		}

		.booking-modal-actions {
			flex-direction: column;
		}

		.booking-modal-actions .btn {
			width: 100%;
		}
	}
</style>

<div class="booking-modal-overlay" id="bookingModal" aria-hidden="true">
	<div class="booking-modal" role="dialog" aria-modal="true" aria-labelledby="bookingModalTitle">
		<div class="booking-modal-header">
			<div>
				<p class="booking-modal-kicker">Booking Details</p>
				<h2 id="bookingModalTitle">Booking #<span id="bookingId">123</span></h2>
			</div>
			<button type="button" class="booking-modal-close" data-booking-modal-close aria-label="Close modal">&times;</button>
		</div>

		<div class="booking-details-grid">
			<div class="booking-detail-item">
				<span class="booking-detail-label">Guest Name</span>
				<span class="booking-detail-value" id="guestName">-</span>
			</div>
			<div class="booking-detail-item">
				<span class="booking-detail-label">Email</span>
				<span class="booking-detail-value" id="guestEmail">-</span>
			</div>
			<div class="booking-detail-item">
				<span class="booking-detail-label">Phone</span>
				<span class="booking-detail-value" id="guestPhone">-</span>
			</div>
			<div class="booking-detail-item">
				<span class="booking-detail-label">Room Type</span>
				<span class="booking-detail-value" id="roomType">-</span>
			</div>
			<div class="booking-detail-item">
				<span class="booking-detail-label">Check-in Date</span>
				<span class="booking-detail-value" id="checkIn">-</span>
			</div>
			<div class="booking-detail-item">
				<span class="booking-detail-label">Check-out Date</span>
				<span class="booking-detail-value" id="checkOut">-</span>
			</div>
			<div class="booking-detail-item">
				<span class="booking-detail-label">Number of Guests</span>
				<span class="booking-detail-value" id="numGuests">-</span>
			</div>
			<div class="booking-detail-item">
				<span class="booking-detail-label">Total Amount</span>
				<span class="booking-detail-value" id="totalAmount">-</span>
			</div>
			<div class="booking-detail-item">
				<span class="booking-detail-label">Payment Status</span>
				<span class="booking-detail-value" id="paymentStatus">-</span>
			</div>
			<div class="booking-detail-item">
				<span class="booking-detail-label">Booking Status</span>
				<span class="booking-status" id="bookingStatus">-</span>
			</div>
			<div class="booking-detail-item" style="grid-column: 1 / -1;">
				<span class="booking-detail-label">Special Requests</span>
				<span class="booking-detail-value" id="specialRequests">-</span>
			</div>
		</div>

		<div class="booking-modal-actions">
			<button type="button" class="btn btn-secondary" data-booking-modal-close>Close</button>
			<form action="/CeylonGo/public/hotel/hotel/booking/reject/" method="POST" id="rejectForm" style="display: inline;">
				<button type="button" class="btn-reject" id="rejectBtn" style="display: none;">Reject Booking</button>
			</form>
			<button type="button" class="btn-accept" id="acceptBtn" style="display: none;">Accept Booking</button>
		</div>
	</div>
</div>

<script>
	document.addEventListener('DOMContentLoaded', function() {
		const modal = document.getElementById('bookingModal');
		const modalTitle = document.getElementById('bookingModalTitle');
		const bookingId = document.getElementById('bookingId');
		const acceptBtn = document.getElementById('acceptBtn');
		const rejectBtn = document.getElementById('rejectBtn');

		// Open modal for viewing booking details
		document.querySelectorAll('[data-booking-modal-open]').forEach(btn => {
			btn.addEventListener('click', function() {
				const bookingData = this.dataset;

				// Populate modal with booking data
				bookingId.textContent = bookingData.bookingId || '';
				document.getElementById('guestName').textContent = bookingData.guestName || '-';
				document.getElementById('guestEmail').textContent = bookingData.guestEmail || '-';
				document.getElementById('guestPhone').textContent = bookingData.guestPhone || '-';
				document.getElementById('roomType').textContent = bookingData.roomType || '-';
				document.getElementById('checkIn').textContent = bookingData.checkIn || '-';
				document.getElementById('checkOut').textContent = bookingData.checkOut || '-';
				document.getElementById('numGuests').textContent = bookingData.numGuests || '-';
				document.getElementById('totalAmount').textContent = bookingData.totalAmount || '-';
				document.getElementById('paymentStatus').textContent = bookingData.paymentStatus || '-';
				document.getElementById('specialRequests').textContent = bookingData.specialRequests || '-';

				// Set booking status with appropriate class
				const statusElement = document.getElementById('bookingStatus');
				const status = bookingData.bookingStatus || 'pending';
				statusElement.textContent = status.charAt(0).toUpperCase() + status.slice(1);
				statusElement.className = 'booking-status ' + status.toLowerCase();

				// Show/hide action buttons based on status
				if (status.toLowerCase() === 'pending') {
					acceptBtn.style.display = 'inline-block';
					rejectBtn.style.display = 'inline-block';
				} else {
					acceptBtn.style.display = 'none';
					rejectBtn.style.display = 'none';
				}

				// Open modal
				modal.classList.add('is-open');
				document.body.classList.add('booking-modal-open');
			});
		});

		// Accept booking
		acceptBtn.addEventListener('click', function() {
			updateBookingStatus(bookingId.textContent, 'confirmed');
		});

		// Reject booking
		rejectBtn.addEventListener('click', function() {
			updateBookingStatus(bookingId.textContent, 'cancelled');
		});

		function updateBookingStatus(bookingId, status) {
			fetch('/CeylonGo/public/hotel/update-booking-status', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded',
				},
				body: new URLSearchParams({
					booking_id: bookingId,
					status: status
				})
			})
			.then(response => response.json())
			.then(data => {
				if (data.success) {
					alert(data.message);
					modal.classList.remove('is-open');
					document.body.classList.remove('booking-modal-open');
					location.reload(); // Refresh to show updated status
				} else {
					alert('Error: ' + data.message);
				}
			})
			.catch(error => {
				console.error('Error:', error);
				alert('An error occurred while updating the booking status.');
			});
		}

		// Close modal when clicking overlay
		modal.addEventListener('click', function(e) {
			if (e.target === modal) {
				modal.classList.remove('is-open');
				document.body.classList.remove('booking-modal-open');
			}
		});
	});
</script>