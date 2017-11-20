clear;
[signal,Fs] = audioread('xbobci00.wav'); signal=signal';

%pocet vzorku (delka ve vzorcich
N = length(signal);

%vzorkovaci frekvence
Fs;

%delka v sekundach
t = N / Fs;

G = 10 * log10(1/N * abs(fft(signal)).^2);
f = (0:N/2-1)/N * Fs; G = G(1:N/2);
%plot modulu spektra
plot (f,G); xlabel('f [Hz]'); ylabel ('PSD [dB]'); grid; 
[pks,locs] = findpeaks(G,'sortstr','descend');

%maximum modulu spektra
locs(1);

%IIR filter
b = [0.2324, -0.4112 0.2324];
a = [1 0.2289 0.4662 ];

%plot nul a polu IIRfiltru
zplane(b,a);

%kmitoctova charakteristika IIRfiltru
H = freqz(b,a,256); f=(0:255) / 256 * Fs / 2; 
subplot(211); plot (f,abs(H)); grid; xlabel('f'); ylabel('|H(f)|');
subplot(212); plot (f,angle(H)); grid; xlabel('f'); ylabel('arg H(f)');

%filtrovani signalu
filtered = filter(b,a,signal);

%modul spektra filtru
Gfiltered = 10 * log10(1/N * abs(fft(filtered)).^2);
f = (0:N/2-1)/N * Fs;
Gfiltered = Gfiltered(1:N/2);
subplot(212);
subplot(211);plot (f,Gfiltered); xlabel('f [Hz]'); ylabel ('PSD [dB]'); grid; 

%maximum modulu spektra filtrovaneho signalu
[pksFilter,locsFilter] = findpeaks(Gfiltered,'sortstr','descend');
locsFilter(1);

%autokorelacni koeficienty
x = signal(1:(0.02*Fs + 0.5*Fs -1));
Nx = length(x);
k = -Nx+1 : Nx-1;
R = xcorr(x, 'biased');
subplot(111); plot(k,R);
%hodnota koeficientu v R(10);
R(10);




