const fs = require('fs');
const path = require('path');

const filePath = path.join(__dirname, 'public', 'data', 'rally-history.json');
const rallyHistory = JSON.parse(fs.readFileSync(filePath, 'utf-8'));

function generateSummary(entry) {
  const title = entry.title || entry.name;
  if (!title) return null;

  return `An important moment in rally history: "${title}". Click to learn more.`;
}

for (const decade in rallyHistory) {
  if (!rallyHistory[decade].events) continue;

  rallyHistory[decade].events.forEach(event => {
    if (!event.summary) {
      const summary = generateSummary(event);
      if (summary) {
        event.summary = summary;
        console.log(`✓ Added summary for ${event.year}: ${event.title || event.name}`);
      }
    }
  });
}

fs.writeFileSync(filePath, JSON.stringify(rallyHistory, null, 2));
console.log('✅ Done adding summaries.');
