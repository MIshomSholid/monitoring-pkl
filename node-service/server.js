/**
 * server.js
 * Realtime Service - Presensi PKL
 */

const express = require("express");
const http = require("http");
const { Server } = require("socket.io");
const cors = require("cors");

const app = express();
app.use(cors());
app.use(express.json());
app.get("/", (req, res) => {
    res.send("Node hidup");
});

app.get("/test", (req, res) => {
    res.send("Route OK");
});

const server = http.createServer(app);

const io = new Server(server, {
    cors: {
        origin: "*",
        methods: ["GET", "POST"],
    },
});

/* ================= SOCKET CONNECTION ================= */

io.on("connection", (socket) => {
    console.log("Client connected:", socket.id);

    socket.on("join-user", (data) => {
        const { role, user_id } = data;

        if (!user_id) return;

        // Join room admin
        if (role === "admin") {
            socket.join("admin");
        }

        // Join room berdasarkan user id
        socket.join(`user_${user_id}`);

        console.log(
            `Socket ${socket.id} joined rooms: ${role || "-"}, user_${user_id}`,
        );
    });

    socket.on("disconnect", () => {
        console.log("Client disconnected:", socket.id);
    });
});

/* ================= LARAVEL BROADCAST ENDPOINT ================= */

app.post("/broadcast", (req, res) => {
    console.log("🔥 MASUK /broadcast");

    const { event, data } = req.body;

    console.log("EVENT:", event);
    console.log("DATA:", data);

    if (!event || !data) {
        console.log("❌ Payload invalid");
        return res.status(400).json({ error: "Invalid payload" });
    }

    // ADMIN
    io.to("admin").emit(event, data);

    // SISWA
    if (data.siswa_user_id) {
        io.to(`user_${data.siswa_user_id}`).emit(event, data);
    }

    // GURU
    if (data.guru_user_id) {
        io.to(`user_${data.guru_user_id}`).emit(event, data);
    }

    // PEMBIMBING
    if (data.pembimbing_user_id) {
        io.to(`user_${data.pembimbing_user_id}`).emit(event, data);
    }

    console.log("✅ Broadcast sukses:", event);

    return res.json({ status: "ok" });
});

/* ================= SERVER START ================= */

const PORT = process.env.PORT;

server.keepAliveTimeout = 65000;
server.headersTimeout = 66000;

server.listen(PORT, () => {
    console.log(`Realtime server running on port ${PORT}`);
});
