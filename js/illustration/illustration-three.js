import * as THREE from 'three';
import { FBXLoader } from 'three/addons/loaders/FBXLoader.js';
import { OrbitControls } from 'three/addons/controls/OrbitControls.js';

const container = document.getElementById('illustration-3d-container');

// --- 1. Scene Setup ---
const scene = new THREE.Scene();
scene.background = new THREE.Color(0x000000);

const camera = new THREE.PerspectiveCamera(45, container.clientWidth / container.clientHeight, 0.1, 1000);
camera.position.set(6, 4, 6);

const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
renderer.setSize(container.clientWidth, container.clientHeight);
renderer.setPixelRatio(window.devicePixelRatio);
container.appendChild(renderer.domElement);

// --- 2. Lighting ---
const ambientLight = new THREE.AmbientLight(0xffffff, 1.0);
scene.add(ambientLight);

const directionalLight = new THREE.DirectionalLight(0xffffff, 1.5);
directionalLight.position.set(10, 20, 10);
scene.add(directionalLight);

// --- 3. Controls ---
const controls = new OrbitControls(camera, renderer.domElement);
controls.enableDamping = true;

// --- 4. Material & Texture ---
const woodMaterial = new THREE.MeshStandardMaterial({ 
    color: 0x9b7653, 
    roughness: 0.7,
    metalness: 0.1
});

const fbxLoader = new FBXLoader();
fbxLoader.load('model/wood/wood.fbx', (fbx) => {
    fbx.traverse((child) => {
        if (child.isMesh && child.material) {
            const mat = Array.isArray(child.material) ? child.material[0] : child.material;
            if (mat.map) {
                woodMaterial.map = mat.map;
                woodMaterial.needsUpdate = true;
            }
        }
    });
});

// --- 5. Geometry Construction: Parallel Side-by-Side ---
const woodGroup = new THREE.Group();

const createBlock = (w, h, d, x, y, z, name) => {
    const mesh = new THREE.Mesh(new THREE.BoxGeometry(w, h, d), woodMaterial);
    mesh.position.set(x, y, z);
    mesh.name = name;
    woodGroup.add(mesh);
    return mesh;
};

// --- BASE LAYER (Ground Level, Y=0.1) ---
// BASE_BEAM_3M (A): Positions along X-axis
const baseBeam3m = createBlock(3.0, 0.2, 0.5, 1.5, 0.1, 0, "BASE_BEAM_3M");

// JOINT_BEAM_2M (B): Parallel and side-by-side with A (following red line)
// Width 0.5m, Length 2.0m, Positioned at Z=0.5
const jointBeam2m = createBlock(2.0, 0.2, 0.5, 1.0, 0.1, 0.5, "JOINT_BEAM_2M");

// --- TOP LAYER (Elevated Level, Y=0.3) ---
// TOP_BEAM_2M (B): Centered laterally (Z=0.25) but shifted 0.02m longitudinally (X=0.98)
const topBeam2m = createBlock(2.0, 0.2, 0.4, 0.98, 0.3, 0.25, "TOP_BEAM_2M");

scene.add(woodGroup);

// --- 6. Technical Annotations ---
const createDot = (pos) => {
    const dot = new THREE.Mesh(new THREE.SphereGeometry(0.02, 16, 16), new THREE.MeshBasicMaterial({ color: 0xffffff }));
    dot.position.copy(pos);
    scene.add(dot);
};

const createDimLine = (start, end) => {
    const line = new THREE.Line(new THREE.BufferGeometry().setFromPoints([start, end]), new THREE.LineBasicMaterial({ color: 0xffffff }));
    scene.add(line);
    createDot(start); createDot(end);
};

const createLabel = (text, pos) => {
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');
    canvas.width = 128; canvas.height = 64;
    ctx.fillStyle = '#ffffff'; ctx.font = 'bold 24px Arial';
    ctx.textAlign = 'center'; ctx.textBaseline = 'middle';
    ctx.fillText(text, 64, 32);

    const texture = new THREE.CanvasTexture(canvas);
    const material = new THREE.SpriteMaterial({ map: texture, transparent: true });
    const sprite = new THREE.Sprite(material);
    sprite.scale.set(0.35, 0.18, 1);
    sprite.position.copy(pos);
    scene.add(sprite);
};

// --- Final Measurements (Matching Image 2 Layout) ---
// 3m Length (A)
createDimLine(new THREE.Vector3(0, 0.2, -0.25), new THREE.Vector3(3.0, 0.2, -0.25));
createLabel("3m", new THREE.Vector3(1.5, 0.35, -0.25));

// 2m Length (C)
createDimLine(new THREE.Vector3(0, 0.2, 0.75), new THREE.Vector3(2.0, 0.2, 0.75));
createLabel("2m", new THREE.Vector3(1.0, 0.35, 0.75));

// 2m Length (B - Top Wood)
createDimLine(new THREE.Vector3(-0.02, 0.4, 0.25), new THREE.Vector3(1.98, 0.4, 0.25));
createLabel("2m", new THREE.Vector3(0.98, 0.55, 0.25));

// Base Widths
createDimLine(new THREE.Vector3(3.0, 0.1, -0.25), new THREE.Vector3(3.0, 0.1, 0.25));
createLabel("0.5m", new THREE.Vector3(3.3, 0.1, 0));

createDimLine(new THREE.Vector3(2.0, 0.1, 0.25), new THREE.Vector3(2.0, 0.1, 0.75));
createLabel("0.5m", new THREE.Vector3(2.3, 0.1, 0.5));

// Top B Width (0.4m)
createDimLine(new THREE.Vector3(1.98, 0.4, 0.05), new THREE.Vector3(1.98, 0.4, 0.45));
createLabel("0.4m", new THREE.Vector3(2.1, 0.53, 0.25));

// 0.2m HEIGHT (Top Beam B)
createDimLine(new THREE.Vector3(1.98, 0.2, 0.45), new THREE.Vector3(1.98, 0.4, 0.45));
createLabel("0.2m", new THREE.Vector3(2.15, 0.3, 0.45));

// 0.02m Gap at the end (Matching Image 2 placement)
createDimLine(new THREE.Vector3(2.0, 0.2, 0.45), new THREE.Vector3(1.98, 0.2, 0.45));
createLabel("0.02m", new THREE.Vector3(2.05, 0.1, 0.45));

// Total Joint Width (Base)
createDimLine(new THREE.Vector3(0, 0, -0.25), new THREE.Vector3(0, 0, 0.75));
createLabel("1.0m", new THREE.Vector3(-0.2, 0.1, 0.25));

// --- 7. Environment & Helpers ---
scene.add(new THREE.AxesHelper(3));

window.addEventListener('resize', () => {
    camera.aspect = container.clientWidth / container.clientHeight;
    camera.updateProjectionMatrix();
    renderer.setSize(container.clientWidth, container.clientHeight);
});

function animate() {
    controls.update();
    renderer.render(scene, camera);
}
renderer.setAnimationLoop(animate);

// Focus Camera on the bundle
camera.position.set(4, 3, 4);
camera.lookAt(1.5, 0, 0.25);
controls.target.set(1.5, 0, 0.25);
controls.update();