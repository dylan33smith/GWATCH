# Gwatch

Full-stack web application with Node.js/Express backend and React frontend.

## Project Structure

- `server/` - Node.js + Express backend
- `client/` - React frontend (Vite)

## Getting Started

### Backend
```bash
cd server
npm install
npm run dev
```

### Frontend
```bash
cd client
npm install
npm run dev
```

## Environment Variables

Create a `.env` file in the `server/` directory with:
```
MONGODB_URI=mongodb+srv://ds85:<db_password>@gwatch-initial-db.osqutfb.mongodb.net/?retryWrites=true&w=majority&appName=Gwatch-Initial-db
``` 