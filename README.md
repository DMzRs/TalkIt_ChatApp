# TalkIt 💬

TalkIt is a real-time group chat web application where users can register an account and instantly send and receive messages with everyone in the room — no page reloads, no refreshing. Messages appear live the moment they're sent.

## Features

- **Real-time messaging** — messages appear instantly for all users using WebSockets
- **User accounts** — register and log in with your name and email
- **Live status indicator** — shows when you're connected to the chat
- **Message grouping** — consecutive messages from the same person are grouped together cleanly
- **Optimistic rendering** — your own messages appear immediately without waiting for the server
- **Warm, modern UI** — clean and friendly design that feels natural to use

## Built With

- **Laravel 13** — PHP web framework
- **Pusher Channels** — WebSocket service powering real-time delivery
- **SQLite** — lightweight database storing messages and user accounts
- **Blade** — Laravel's templating engine for the frontend

## About

TalkIt was built as a portfolio project to demonstrate full-stack web development skills including authentication, real-time event broadcasting, database design, and frontend UI development.