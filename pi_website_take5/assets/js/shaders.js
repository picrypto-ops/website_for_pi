/**
 * WebGL shaders for the particle wave effect
 * Used by pi_bg_wave.js
 */

// Vertex Shader
export const vertexShader = `
    precision mediump float;

    attribute float scale;

    void main() {
        vec4 mvPosition = modelViewMatrix * vec4(position, 1.0);
        gl_PointSize = scale * (450.0 / -mvPosition.z);
        gl_Position = projectionMatrix * mvPosition;
    }
`;

// Fragment Shader
export const fragmentShader = `
    precision mediump float;

    uniform sampler2D pointTexture;
    uniform vec3 color;

    varying float vDigitIndex;

    void main() {
        vec4 texColor = texture2D(pointTexture, vec2(gl_PointCoord.x,1.0-gl_PointCoord.y));
        if (texColor.a < 0.1) discard; // Discard transparent pixels
        gl_FragColor = vec4(color, 1.0) * texColor;
    }
`; 