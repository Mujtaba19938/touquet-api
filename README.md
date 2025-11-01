# Touqet API

A PHP backend API for Islamic Fiqh Q&A with vector search capabilities, OpenAI embeddings, and Mufti connect functionality.

## Features

- 🔍 **Vector Search**: Semantic search using OpenAI embeddings for Fiqh questions
- 📚 **Fiqh QA**: Ask questions and get answers from Islamic texts
- 👨‍🎓 **Mufti Connect**: Connect with qualified Muftis via WhatsApp
- 🔐 **API Key Authentication**: Secure API access
- 🚀 **Fast & Lightweight**: Pure PHP with PDO for database operations

## Tech Stack

- **PHP** 7.4+ (PDO, cURL)
- **MySQL** 8.0+ (JSON column support)
- **OpenAI API** for embeddings (`text-embedding-3-small`)
- **Composer** for dependency management

## Setup

### Requirements

- PHP 7.4 or higher
- MySQL 8.0 or higher
- Composer
- OpenAI API key

### Installation

1. Clone the repository:
```bash
git clone https://github.com/Mujtaba19938/touquet-api.git
cd touquet-api
```

2. Install dependencies:
```bash
composer install
```

3. Create environment file:
```bash
cp .env.example .env
```

4. Configure environment variables in `.env`:
```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=touquet
DB_USER=root
DB_PASS=

OPENAI_API_KEY=your_openai_api_key
OPENAI_EMBED_MODEL=text-embedding-3-small

API_KEY=your_secret_api_key_for_admin_endpoints

WHATSAPP_NUMBER=+923001234567
```

5. Create database:
```bash
mysql -u root -p < sql/schema.sql
```

6. Point your web server to the `public/` directory:
   - Apache: Configure VirtualHost to serve from `public/`
   - Nginx: Set root to `public/`
   - For local development: Use PHP built-in server:
     ```bash
     php -S localhost:8000 -t public
     ```

## API Endpoints

### Health Check
```
GET /api/health
```
Returns server status.

### Ask Question
```
POST /api/fiqh/ask
Content-Type: application/json

{
  "question": "What is the ruling on missing prayers?"
}
```
Returns relevant Fiqh answers with citations.

### Upsert Fiqh Text (Admin)
```
POST /api/fiqh/upsert
Content-Type: application/json
X-API-KEY: your_api_key

{
  "book": "Fatawa Rahimiyyah",
  "chapter": "Salah",
  "text": "Missed prayers must be made up...",
  "lang": "ur",
  "ref": "Vol. 2, p. 145"
}
```

### Connect to Mufti
```
POST /api/mufti/connect
Content-Type: application/json

{
  "name": "John Doe",
  "question": "Need clarification on...",
  "madhhab": "Hanafi",
  "lang": "en"
}
```
Returns WhatsApp deep link.

## Database Schema

- `fiqh_books`: Islamic Fiqh books
- `fiqh_texts`: Text chunks with citations
- `fiqh_embeddings`: Vector embeddings for texts
- `queries`: User queries log
- `muftis`: Available Muftis
- `users`: User profiles (optional)

## Development

### Adding New Routes

1. Create route file in `routes/`
2. Add route mapping in `public/index.php`
3. Implement handler with proper error handling

### Testing

```bash
# Health check
curl http://localhost:8000/api/health

# Ask question
curl -X POST http://localhost:8000/api/fiqh/ask \
  -H "Content-Type: application/json" \
  -d '{"question":"What is wudu?"}'
```

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

MIT License - feel free to use this project for your own purposes.

## Author

Mujtaba Khanani

## Support

For issues and questions, please open an issue on GitHub.

