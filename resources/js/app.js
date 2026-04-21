import './bootstrap';

import Alpine from 'alpinejs';
import * as Presensi from './presensi/index';
import { io } from "socket.io-client";

window.Presensi = Presensi;

window.Alpine = Alpine;

window.io = io;

Alpine.start();
