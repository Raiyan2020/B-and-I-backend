(function(window) {
    'use strict';

    var audioContext = null;
    var soundBuffer = null;
    var htmlAudio = null;
    var soundUrl = null;
    var unlocked = false;
    var unlockPromise = null;
    var broadcastChannel = null;

    function getAudioContext() {
        if (!audioContext) {
            var AudioContextClass = window.AudioContext || window.webkitAudioContext;
            if (!AudioContextClass) {
                return null;
            }
            audioContext = new AudioContextClass();
        }
        return audioContext;
    }

    function loadSoundBuffer() {
        if (!soundUrl || soundBuffer) {
            return Promise.resolve(soundBuffer);
        }

        var context = getAudioContext();
        if (!context) {
            return Promise.resolve(null);
        }

        return fetch(soundUrl, { cache: 'force-cache' })
            .then(function(response) {
                if (!response.ok) {
                    throw new Error('Failed to load notification sound');
                }
                return response.arrayBuffer();
            })
            .then(function(arrayBuffer) {
                return context.decodeAudioData(arrayBuffer);
            })
            .then(function(buffer) {
                soundBuffer = buffer;
                return buffer;
            })
            .catch(function() {
                return null;
            });
    }

    function playOscillatorFallback() {
        var context = getAudioContext();
        if (!context) {
            return Promise.resolve(false);
        }

        var playTone = function() {
            var now = context.currentTime;
            var oscillator = context.createOscillator();
            var gain = context.createGain();

            oscillator.type = 'sine';
            oscillator.frequency.setValueAtTime(880, now);
            oscillator.frequency.setValueAtTime(1174.66, now + 0.12);

            gain.gain.setValueAtTime(0.0001, now);
            gain.gain.exponentialRampToValueAtTime(0.45, now + 0.02);
            gain.gain.exponentialRampToValueAtTime(0.0001, now + 0.35);

            oscillator.connect(gain);
            gain.connect(context.destination);
            oscillator.start(now);
            oscillator.stop(now + 0.36);

            return true;
        };

        if (context.state === 'suspended') {
            return context.resume()
                .then(playTone)
                .catch(function() {
                    return false;
                });
        }

        return Promise.resolve(playTone());
    }

    function playBuffer() {
        var context = getAudioContext();
        if (!context || !soundBuffer) {
            return Promise.resolve(false);
        }

        var startPlayback = function() {
            var source = context.createBufferSource();
            source.buffer = soundBuffer;
            source.connect(context.destination);
            source.start(0);
            return true;
        };

        if (context.state === 'suspended') {
            return context.resume()
                .then(startPlayback)
                .catch(function() {
                    return false;
                });
        }

        return Promise.resolve(startPlayback());
    }

    function playHtmlAudioFallback() {
        if (!soundUrl) {
            return Promise.resolve(false);
        }

        if (!htmlAudio) {
            htmlAudio = new Audio(soundUrl);
            htmlAudio.preload = 'auto';
        }

        htmlAudio.currentTime = 0;

        return htmlAudio.play()
            .then(function() {
                return true;
            })
            .catch(function() {
                return false;
            });
    }

    function bindUnlockEvents() {
        var events = ['click', 'keydown', 'touchstart', 'pointerdown'];

        events.forEach(function(eventName) {
            document.addEventListener(eventName, function() {
                window.AdminNotificationSound.unlock();
            }, { passive: true });
        });

        var bellTrigger = document.querySelector('.dropdown-notification > a');
        if (bellTrigger) {
            bellTrigger.addEventListener('click', function() {
                window.AdminNotificationSound.unlock();
            });
        }
    }

    function bindBroadcastChannel() {
        if (!('BroadcastChannel' in window)) {
            return;
        }

        broadcastChannel = new BroadcastChannel('admin-notification-sound');
        broadcastChannel.onmessage = function(event) {
            if (event.data && event.data.type === 'ADMIN_NOTIFICATION_SOUND') {
                window.AdminNotificationSound.play();
            }
        };
    }

    window.AdminNotificationSound = {
        init: function(url) {
            soundUrl = url || null;
            bindUnlockEvents();
            bindBroadcastChannel();
            loadSoundBuffer();

            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.addEventListener('message', function(event) {
                    if (event.data && event.data.type === 'ADMIN_NOTIFICATION_SOUND') {
                        window.AdminNotificationSound.play();
                    }
                });
            }
        },

        unlock: function() {
            if (unlocked) {
                return Promise.resolve(true);
            }

            if (unlockPromise) {
                return unlockPromise;
            }

            unlockPromise = Promise.resolve()
                .then(function() {
                    var context = getAudioContext();
                    if (context && context.state === 'suspended') {
                        return context.resume();
                    }
                })
                .then(function() {
                    return loadSoundBuffer();
                })
                .then(function() {
                    unlocked = true;
                    return true;
                })
                .catch(function() {
                    unlockPromise = null;
                    return false;
                });

            return unlockPromise;
        },

        play: function() {
            var self = this;

            return this.unlock()
                .catch(function() {
                    return false;
                })
                .then(function() {
                    return loadSoundBuffer();
                })
                .then(function() {
                    return playBuffer();
                })
                .then(function(played) {
                    if (played) {
                        return true;
                    }

                    return playHtmlAudioFallback();
                })
                .then(function(played) {
                    if (played) {
                        return true;
                    }

                    return playOscillatorFallback();
                })
                .then(function(played) {
                    if (!played && !unlocked) {
                        self.unlock();
                    }

                    return played;
                });
        },
    };
})(window);
