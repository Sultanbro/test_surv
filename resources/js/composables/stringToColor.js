/********************************************************
Name: stringToColor
Description: create a hash from a string then generates a color
Usage: alert('#'+stringToColor("Any string can be converted"));
author: Brandon Corbin [code@icorbin.com]
website: http://icorbin.com
********************************************************/

// Generate a Hash for the String
export function hash(word) {
	var h = 0;
	for (var i = 0; i < word.length; ++i) {
		h = word.charCodeAt(i) + ((h << 5) - h);
	}
	return h;
}

// Change the darkness or lightness
export function shade(color, prc) {
	var num = parseInt(color, 16),
		amt = Math.round(2.55 * prc),
		R = (num >> 16) + amt,
		G = (num >> 8 & 0x00FF) + amt,
		B = (num & 0x0000FF) + amt;
	return (0x1000000 + (R < 255 ? R < 1 ? 0 : R : 255) * 0x10000 +
		(G < 255 ? G < 1 ? 0 : G : 255) * 0x100 +
		(B < 255 ? B < 1 ? 0 : B : 255))
		.toString(16)
		.slice(1);
}

// Convert init to an RGBA
export function intToRGBA(i) {
	return ((i >> 24) & 0xFF).toString(16) +
		((i >> 16) & 0xFF).toString(16) +
		((i >> 8) & 0xFF).toString(16) +
		(i & 0xFF).toString(16)
}

export function stringToColor(str) {
	return shade(intToRGBA(hash(str)), -10)
}
