const http = require('http');
const fs = require('fs');
const path = require('path');

const PORT = 80;
const DB_FILE = path.join(__dirname, 'mock_db.json');
const UPLOADS_DIR = path.join(__dirname, 'uploads');

// Create uploads directory if it doesn't exist
if (!fs.existsSync(UPLOADS_DIR)) {
  fs.mkdirSync(UPLOADS_DIR, { recursive: true });
}

// Initialize database file if it doesn't exist
if (!fs.existsSync(DB_FILE)) {
  fs.writeFileSync(DB_FILE, JSON.stringify({ users: [], locations: [], visits: [] }, null, 2));
}

function readDB() {
  return JSON.parse(fs.readFileSync(DB_FILE, 'utf8'));
}

function writeDB(data) {
  fs.writeFileSync(DB_FILE, JSON.stringify(data, null, 2));
}

// CORS Headers helper
function setCORSHeaders(res) {
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'POST, GET, PUT, DELETE, OPTIONS');
  res.setHeader('Access-Control-Allow-Headers', 'Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');
  res.setHeader('Content-Type', 'application/json');
}

const server = http.createServer((req, res) => {
  setCORSHeaders(res);

  // Handle CORS preflight
  if (req.method === 'OPTIONS') {
    res.statusCode = 200;
    res.end();
    return;
  }

  // Normalize URL paths
  let urlPath = req.url.split('?')[0];
  const queryParams = {};
  if (req.url.includes('?')) {
    const qStr = req.url.split('?')[1];
    qStr.split('&').forEach(part => {
      const [key, val] = part.split('=');
      queryParams[key] = decodeURIComponent(val || '');
    });
  }

  // Support absolute routes like /Salesman-Tracking-System/backend/auth/login.php
  urlPath = urlPath.replace('/Salesman-Tracking-System/backend', '');

  // Static files server for uploads
  if (urlPath.startsWith('/uploads/')) {
    const filename = urlPath.replace('/uploads/', '');
    const filepath = path.join(UPLOADS_DIR, filename);
    if (fs.existsSync(filepath)) {
      res.setHeader('Content-Type', 'image/jpeg');
      fs.createReadStream(filepath).pipe(res);
    } else {
      res.statusCode = 404;
      res.end(JSON.stringify({ message: 'Image not found' }));
    }
    return;
  }

  // Parse Body for POST/PUT requests
  let body = '';
  req.on('data', chunk => {
    body += chunk.toString();
  });

  req.on('end', () => {
    let postData = {};
    if (body) {
      try {
        postData = JSON.parse(body);
      } catch (e) {
        // Fallback for form-data if any
      }
    }

    try {
      // 1. REGISTER
      if (urlPath === '/auth/register.php' && req.method === 'POST') {
        const db = readDB();
        const { name, email, phone, password, role } = postData;

        if (!name || !email || !phone || !password || !role) {
          res.statusCode = 400;
          res.end(JSON.stringify({ message: 'Data is incomplete.' }));
          return;
        }

        if (db.users.some(u => u.email === email)) {
          res.statusCode = 400;
          res.end(JSON.stringify({ message: 'Email is already registered.' }));
          return;
        }

        const newUser = {
          id: db.users.length + 1,
          name,
          email,
          phone,
          password, // Storing password directly for mock simplicity
          role,
          created_at: new Date().toISOString()
        };

        db.users.push(newUser);
        writeDB(db);

        res.statusCode = 201;
        res.end(JSON.stringify({
          message: 'User was registered successfully.',
          user: { name, email, phone, role }
        }));
        return;
      }

      // 2. LOGIN
      if (urlPath === '/auth/login.php' && req.method === 'POST') {
        const db = readDB();
        const { email, password } = postData;

        const user = db.users.find(u => u.email === email && u.password === password);
        if (user) {
          const userSafe = { ...user };
          delete userSafe.password;
          res.statusCode = 200;
          res.end(JSON.stringify({ message: 'Login successful.', user: userSafe }));
        } else {
          res.statusCode = 401;
          res.end(JSON.stringify({ message: 'Login failed. Invalid credentials.' }));
        }
        return;
      }

      // 3. GET SALESPERSONS (Manager only)
      if (urlPath === '/auth/salespersons.php' && req.method === 'GET') {
        const db = readDB();
        const salespersons = db.users
          .filter(u => u.role === 'salesman')
          .map(u => ({ id: u.id, name: u.name, email: u.email, phone: u.phone, created_at: u.created_at }));
        
        res.statusCode = 200;
        res.end(JSON.stringify(salespersons));
        return;
      }

      // 4. SAVE LOCATION
      if (urlPath === '/location/save.php' && req.method === 'POST') {
        const db = readDB();
        const { user_id, latitude, longitude } = postData;

        if (!user_id || latitude === undefined || longitude === undefined) {
          res.statusCode = 400;
          res.end(JSON.stringify({ message: 'Data is incomplete.' }));
          return;
        }

        const newLoc = {
          id: db.locations.length + 1,
          user_id: parseInt(user_id),
          latitude: parseFloat(latitude),
          longitude: parseFloat(longitude),
          tracked_at: new Date().toISOString()
        };

        db.locations.push(newLoc);
        writeDB(db);

        res.statusCode = 201;
        res.end(JSON.stringify({ message: 'Location logged successfully.' }));
        return;
      }

      // 5. GET LOCATION HISTORY
      if (urlPath === '/location/history.php' && req.method === 'GET') {
        const db = readDB();
        const userId = parseInt(queryParams.user_id || '0');

        const history = db.locations
          .filter(l => l.user_id === userId)
          .sort((a, b) => new Date(b.tracked_at) - new Date(a.tracked_at));

        res.statusCode = 200;
        res.end(JSON.stringify(history));
        return;
      }

      // 6. ADD VISIT (supports base64 image parsing)
      if (urlPath === '/visit/add.php' && req.method === 'POST') {
        const db = readDB();
        const { user_id, customer_name, customer_address, purpose, notes, latitude, longitude, image } = postData;

        if (!user_id || !customer_name || !customer_address || !purpose || latitude === undefined || longitude === undefined) {
          res.statusCode = 400;
          res.end(JSON.stringify({ message: 'Data is incomplete.' }));
          return;
        }

        let imageFilename = null;
        if (image && image.startsWith('data:image')) {
          // Parse base64 image
          const matches = image.match(/^data:image\/([A-Za-z-+\/]+);base64,(.+)$/);
          if (matches && matches.length === 3) {
            const ext = matches[1] === 'jpeg' ? 'jpg' : matches[1];
            const dataBuffer = Buffer.from(matches[2], 'base64');
            imageFilename = `visit_${Date.now()}_${Math.random().toString(36).substring(2, 8)}.${ext}`;
            fs.writeFileSync(path.join(UPLOADS_DIR, imageFilename), dataBuffer);
          }
        }

        const newVisit = {
          id: db.visits.length + 1,
          user_id: parseInt(user_id),
          customer_name,
          customer_address,
          purpose,
          notes: notes || '',
          latitude: parseFloat(latitude),
          longitude: parseFloat(longitude),
          image: imageFilename,
          visit_date: new Date().toISOString()
        };

        db.visits.push(newVisit);
        writeDB(db);

        res.statusCode = 201;
        res.end(JSON.stringify({ message: 'Visit was logged successfully.', visit: newVisit }));
        return;
      }

      // 7. LIST VISITS
      if (urlPath === '/visit/list.php' && req.method === 'GET') {
        const db = readDB();
        const filterUserId = parseInt(queryParams.user_id || '0');
        const filterDate = queryParams.date || '';

        let results = db.visits.map(v => {
          const salesman = db.users.find(u => u.id === v.user_id) || {};
          return {
            ...v,
            salesman_name: salesman.name || 'Unknown',
            salesman_email: salesman.email || 'N/A',
            salesman_phone: salesman.phone || 'N/A'
          };
        });

        if (filterUserId > 0) {
          results = results.filter(v => v.user_id === filterUserId);
        }

        if (filterDate) {
          results = results.filter(v => v.visit_date.startsWith(filterDate));
        }

        results.sort((a, b) => new Date(b.visit_date) - new Date(a.visit_date));

        res.statusCode = 200;
        res.end(JSON.stringify(results));
        return;
      }

      // 8. DELETE VISIT
      if (urlPath === '/visit/delete.php' && req.method === 'POST') {
        const db = readDB();
        const visitId = parseInt(postData.id || '0');

        const visitIndex = db.visits.findIndex(v => v.id === visitId);
        if (visitIndex !== -1) {
          const visit = db.visits[visitIndex];
          // Delete photo from filesystem if exists
          if (visit.image) {
            const filepath = path.join(UPLOADS_DIR, visit.image);
            if (fs.existsSync(filepath)) {
              fs.unlinkSync(filepath);
            }
          }

          db.visits.splice(visitIndex, 1);
          writeDB(db);

          res.statusCode = 200;
          res.end(JSON.stringify({ message: 'Visit was deleted successfully.' }));
        } else {
          res.statusCode = 404;
          res.end(JSON.stringify({ message: 'Visit not found.' }));
        }
        return;
      }

      // 9. UPDATE VISIT
      if (urlPath === '/visit/update.php' && req.method === 'POST') {
        const db = readDB();
        const { id, customer_name, customer_address, purpose, notes, latitude, longitude, image } = postData;
        const visitId = parseInt(id || '0');

        const visitIndex = db.visits.findIndex(v => v.id === visitId);
        if (visitIndex !== -1) {
          const visit = db.visits[visitIndex];
          visit.customer_name = customer_name || visit.customer_name;
          visit.customer_address = customer_address || visit.customer_address;
          visit.purpose = purpose || visit.purpose;
          visit.notes = notes !== undefined ? notes : visit.notes;
          
          if (latitude !== undefined) visit.latitude = parseFloat(latitude);
          if (longitude !== undefined) visit.longitude = parseFloat(longitude);

          if (image && image.startsWith('data:image')) {
            // Delete old photo
            if (visit.image) {
              const filepath = path.join(UPLOADS_DIR, visit.image);
              if (fs.existsSync(filepath)) {
                fs.unlinkSync(filepath);
              }
            }
            // Write new photo
            const matches = image.match(/^data:image\/([A-Za-z-+\/]+);base64,(.+)$/);
            if (matches && matches.length === 3) {
              const ext = matches[1] === 'jpeg' ? 'jpg' : matches[1];
              const dataBuffer = Buffer.from(matches[2], 'base64');
              visit.image = `visit_${Date.now()}_${Math.random().toString(36).substring(2, 8)}.${ext}`;
              fs.writeFileSync(path.join(UPLOADS_DIR, visit.image), dataBuffer);
            }
          }

          db.visits[visitIndex] = visit;
          writeDB(db);

          res.statusCode = 200;
          res.end(JSON.stringify({ message: 'Visit was updated successfully.' }));
        } else {
          res.statusCode = 404;
          res.end(JSON.stringify({ message: 'Visit not found.' }));
        }
        return;
      }

      // Fallback
      res.statusCode = 404;
      res.end(JSON.stringify({ message: 'Endpoint not found' }));
    } catch (e) {
      console.error(e);
      res.statusCode = 500;
      res.end(JSON.stringify({ message: 'Internal Server Error', error: e.message }));
    }
  });
});

server.listen(PORT, () => {
  console.log(`Mock Backend Server is running locally on port ${PORT}`);
  console.log(`Endpoints base URL: http://localhost/Salesman-Tracking-System/backend/`);
});
