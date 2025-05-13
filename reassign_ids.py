import json

INPUT_FILE = './public/data/rally-history.json'
OUTPUT_FILE = './public/data/rally-history.json'

# ID starting points
EVENT_START_ID = 1
DRIVER_START_ID = 10001
CAR_START_ID = 20001

def reassign_ids(data):
    event_id = EVENT_START_ID
    driver_id = DRIVER_START_ID
    car_id = CAR_START_ID

    for decade, group in data.items():
        if 'events' in group and isinstance(group['events'], list):
            group['events'].sort(key=lambda x: x.get('year', 0))
            for event in group['events']:
                event['id'] = event_id
                event_id += 1

        if 'drivers' in group and isinstance(group['drivers'], list):
            group['drivers'].sort(key=lambda x: x.get('name', ''))
            for driver in group['drivers']:
                driver['id'] = driver_id
                driver_id += 1

        if 'cars' in group and isinstance(group['cars'], list):
            group['cars'].sort(key=lambda x: x.get('name', ''))
            for car in group['cars']:
                car['id'] = car_id
                car_id += 1

    return data

def main():
    with open(INPUT_FILE, 'r', encoding='utf-8') as f:
        data = json.load(f)

    updated_data = reassign_ids(data)

    with open(OUTPUT_FILE, 'w', encoding='utf-8') as f:
        json.dump(updated_data, f, indent=2, ensure_ascii=False)

    print(f"✅ rally-history.json updated successfully with new IDs.")

if __name__ == '__main__':
    main()

# This is what to run in the terminal
#
# python reassign_ids_rally_history.py
