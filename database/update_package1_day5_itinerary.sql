-- Enrich Cultural Triangle package (id=1) Day 5 activities (run once on existing DBs).
-- New installs: already reflected in main.sql seed.

UPDATE `packages`
SET `itinerary` = JSON_REPLACE(
  `itinerary`,
  '$[4]',
  JSON_OBJECT(
    'day', 5,
    'title', 'Nuwara Eliya to Colombo - Departure',
    'activities', JSON_ARRAY(
      'Breakfast at your hotel in Nuwara Eliya',
      'Morning at leisure: optional walk at Gregory Lake or Victoria Park',
      'Scenic drive through tea country toward Colombo with photo stops',
      'Optional shopping break in a hill town en route',
      'Departure'
    )
  )
)
WHERE `id` = 1;
