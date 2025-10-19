<?php
// views/tourist/tourist_dashboard.php

// Process customize trip form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $people = $_POST['people'];
  $destination = $_POST['destination'];
  $days = $_POST['days'];
  $transport = $_POST['transport'];
  $guide = $_POST['guide'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ceylon Go - Plan Your Trip</title>
  <link rel="stylesheet" href="../../public/css/common.css">
  <link rel="stylesheet" href="../../public/css/tourist/tourist_dashboard.css">
  <link rel="stylesheet" href="../../public/css/tourist/navbar.css">
  <link rel="stylesheet" href="../../public/css/tourist/footer.css">

</head>
<body class="bg-app">
  <!-- Navbar include -->
  <?php include 'header.php'; ?>

  <section class="intro">
    <h1>Plan Your Perfect Trip to Sri Lanka</h1>
    <p>Explore the beauty of Sri Lanka with our customizable tour packages.</p>

    <a href="../register.php" class="btn">Get Started</a>
  </section>

  <section class="recommended-packages">
    <h2>Recommended Packages</h2>
    <a href="recommended_packages.php" class="btn btn-black">See All Packages</a>
    <div class="packages">
      <div class="package">
        <div class="package-image"></div>
        <h3>Unawatuna Package</h3>
        <p>3 Days, 2 Nights</p>
      </div>
      <div class="package">
        <div class="package-image"></div>
        <h3>Hill Country Package</h3>
        <p>4 Days, 3 Nights</p>
      </div>
      <div class="package">
        <div class="package-image"></div>
        <h3>Sigiriya Package</h3>
        <p>5 Days, 4 Nights</p>
      </div>
    </div>
  </section>

  <!-- ✅ NEW CUSTOMIZE YOUR TRIP SECTION STARTS HERE -->
  <section id="customize" class="customize-trip">
    <h2>Customize Your Trip</h2>

    <form method="POST" action="">
      <div id="trip-group-container">
        <div class="trip-group">
          <div class="row row-1">
            <div class="box">
              <input type="number" name="people" min="1" placeholder="No of People" required>
            </div>
          </div>

          <div class="row row-2 three">
            <div class="box">
              <div class="box-title">Where are You Going?</div>
              <select name="destination" required>
                <option value="">Select Destination</option>
                <option value="Kandy">Kandy</option>
                <option value="Colombo">Colombo</option>
                <option value="Galle">Galle</option>
              </select>
            </div>
            <div class="box">
              <div class="box-title">How Many Days Do You Plan To Stay There?</div>
              <input type="number" name="days" min="1" placeholder="Days" required>
            </div>
            <div class="box">
              <div class="inline-control">
                <a href="choose_hotel.php" class="btn-black">Choose Hotel</a>
              </div>
            </div>
          </div>

          <div class="row row-3">
            <div class="box">
              <div class="inline-control">
                <div class="box-title" style="margin:0;">Do You Want Transport?</div>
                <div class="btn-group">
                  <input type="hidden" name="transport" value="">
                  <a href="transport_providers.php" class="btn-black" onclick="this.closest('.inline-control').querySelector('input[name=\'transport\']').value='Yes'">Yes</a>
                  <button type="button" class="btn-white" onclick="this.closest('.inline-control').querySelector('input[name=\'transport\']').value='No'">No</button>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>

      <div class="row row-4 actions-right">
        <button type="button" id="addMore" class="btn-white">Add More +</button>
      </div>

      <div class="row row-5">
        <div class="box">
          <div class="inline-control">
            <div class="box-title" style="margin:0;">Add a Tour Guide?</div>
            <div class="btn-group">
              <input type="hidden" name="guide" id="guideChoice" value="">
              <a href="tour_guide_request.html" class="btn-black" onclick="document.getElementById('guideChoice').value='Yes'">Yes</a>
              <button type="button" class="btn-white" onclick="document.getElementById('guideChoice').value='No'">No</button>
            </div>
          </div>
        </div>
      </div>

      <div class="center">
        <button type="submit" class="btn-black finish">Finish</button>
      </div>
    </form>

    <script>
      (function(){
        var addBtn = document.getElementById('addMore');
        if (!addBtn) return;
        addBtn.addEventListener('click', function(){
          var container = document.getElementById('trip-group-container');
          var groups = container.getElementsByClassName('trip-group');
          if (!groups.length) return;
          var clone = groups[0].cloneNode(true);

          // reset inputs in clone
          var inputs = clone.querySelectorAll('input[type=number], input[type=text], input[type=hidden], select');
          inputs.forEach(function(el){
            if (el.tagName.toLowerCase() === 'select') { el.selectedIndex = 0; }
            else { el.value = ''; }
          });

          // add remove button for this clone
          var removeWrap = document.createElement('div');
          removeWrap.className = 'actions-right';
          var removeBtn = document.createElement('button');
          removeBtn.type = 'button';
          removeBtn.className = 'btn-danger';
          removeBtn.textContent = 'Remove';
          removeBtn.addEventListener('click', function(){
            var tg = this.closest('.trip-group');
            if (tg) tg.remove();
          });
          removeWrap.appendChild(removeBtn);
          clone.appendChild(removeWrap);

          container.appendChild(clone);
        });
      })();
    </script>

    <?php if (!empty($people)): ?>
      <div class="summary">
        <h3>Trip Summary</h3>
        <p><strong>No of People:</strong> <?= htmlspecialchars($people) ?></p>
        <p><strong>Destination:</strong> <?= htmlspecialchars($destination) ?></p>
        <p><strong>Days:</strong> <?= htmlspecialchars($days) ?></p>
        <p><strong>Transport:</strong> <?= htmlspecialchars($transport) ?></p>
        <p><strong>Tour Guide:</strong> <?= htmlspecialchars($guide) ?></p>
      </div>
    <?php endif; ?>
  </section>
  <!-- ✅ END CUSTOMIZE YOUR TRIP SECTION -->


  <section class="recommended-destinations">
    <h2>Recommended Destinations</h2>
    <div class="destinations">
      <div class="destination">
        <div class="destination-image"></div>
        <p>Heritance Tea Factory</p>
        <p>Nuwara Eliya</p>
      </div>
      <div class="destination">
        <div class="destination-image"></div>
        <p>Cinnamon Resort</p>
        <p>Unawatuna</p>
      </div>
      <div class="destination">
        <div class="destination-image"></div>
        <p>Hotel Sigiriya</p>
        <p>Sigiriya</p>
      </div>
    </div>
  </section>

  <section class="client-reviews">
    <h2>What Our Clients Say</h2>
    <div class="reviews">
      <div class="review">
        <p>Alice</p>
        <div class="stars">⭐⭐⭐⭐</div>
        <p>An unforgettable experience!</p>
      </div>
      <div class="review">
        <p>Sara</p>
        <div class="stars">⭐⭐⭐⭐⭐</div>
        <p>Everything was well-organized and exciting!</p>
      </div>
      <div class="review">
        <p>John</p>
        <div class="stars">⭐⭐⭐⭐⭐</div>
        <p>Highly recommend customizing your trip.</p>
      </div>
    </div>
  </section>

  <!-- Navbar include -->
  <?php include 'footer.php'; ?>

</body>
</html>
