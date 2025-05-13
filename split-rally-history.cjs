const fs = require('fs');

try {
  const rawData = fs.readFileSync('./public/data/rally-history.json', 'utf-8');
  const parsed = JSON.parse(rawData);

  if (typeof parsed !== 'object') {
    console.error("❌ rally-history.json is not a valid object.");
    process.exit(1);
  }

  for (const [decade, group] of Object.entries(parsed)) {
    const cleanDecade = `${decade}s`;

    if (Array.isArray(group.events)) {
      const eventsPath = `./public/data/events-${cleanDecade}.json`;
      fs.writeFileSync(eventsPath, JSON.stringify(group.events, null, 2));
      console.log(`✅ Written: ${eventsPath}`);
    }

    if (Array.isArray(group.drivers)) {
      const driversPath = `./public/data/drivers-${cleanDecade}.json`;
      fs.writeFileSync(driversPath, JSON.stringify(group.drivers, null, 2));
      console.log(`✅ Written: ${driversPath}`);
    }

    if (Array.isArray(group.cars)) {
      const carsPath = `./public/data/cars-${cleanDecade}.json`;
      fs.writeFileSync(carsPath, JSON.stringify(group.cars, null, 2));
      console.log(`✅ Written: ${carsPath}`);
    }
  }

} catch (err) {
  console.error("🚨 Error processing rally-history.json:");
  console.error(err);
}
// This is what to run in the terminal
// node split - rally - history.cjs
