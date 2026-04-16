'use strict';

/** ============================ Beam Analysis Data Type ============================ */

/**
 * Beam material specification.
 *
 * @param {String} name         Material name
 * @param {Object} properties   Material properties {EI : 0, GA : 0, ....}
 */
class Material {
    constructor(name, properties) {
        this.name = name;
        this.properties = properties;
    }
}

/**
 *
 * @param {Number} primarySpan          Beam primary span length
 * @param {Number} secondarySpan        Beam secondary span length
 * @param {Material} material           Beam material object
 */
class Beam {
    constructor(primarySpan, secondarySpan, material) {
        this.primarySpan = primarySpan;
        this.secondarySpan = secondarySpan;
        this.material = material;
    }
}

/** ============================ Beam Analysis Class ============================ */

class BeamAnalysis {
    constructor() {
        this.options = {
            condition: 'simply-supported'
        };

        this.analyzer = {
            'simply-supported': new BeamAnalysis.analyzer.simplySupported(),
            'two-span-unequal': new BeamAnalysis.analyzer.twoSpanUnequal()
        };
    }
    /**
     *
     * @param {Beam} beam
     * @param {Number} load
     */
    getDeflection(beam, load, condition) {
        var analyzer = this.analyzer[condition];

        if (analyzer) {
            return {
                beam: beam,
                load: load,
                equation: analyzer.getDeflectionEquation(beam, load)
            };
        } else {
            throw new Error('Invalid condition');
        }
    }
    getBendingMoment(beam, load, condition) {
        var analyzer = this.analyzer[condition];

        if (analyzer) {
            return {
                beam: beam,
                load: load,
                equation: analyzer.getBendingMomentEquation(beam, load)
            };
        } else {
            throw new Error('Invalid condition');
        }
    }
    getShearForce(beam, load, condition) {
        var analyzer = this.analyzer[condition];

        if (analyzer) {
            return {
                beam: beam,
                load: load,
                equation: analyzer.getShearForceEquation(beam, load)
            };
        } else {
            throw new Error('Invalid condition');
        }
    }
}




/** ============================ Beam Analysis Analyzer ============================ */

/**
 * Available analyzers for different conditions
 */
BeamAnalysis.analyzer = {};

/**
 * Calculate deflection, bending stress and shear stress for a simply supported beam
 *
 * @param {Beam}   beam   The beam object
 * @param {Number}  load    The applied load
 */
BeamAnalysis.analyzer.simplySupported = class {
    constructor(beam, load) {
        this.beam = beam;
        this.load = load;
    }
    getDeflectionEquation(beam, load) {
        var L = beam.primarySpan;
        var EI = beam.material.properties.EI / 1000000000; // Nmm2 -> kNm2
        var R = (load * L) / 2;
        var C1 = (load * Math.pow(L, 3) / 24) - (R * Math.pow(L, 2) / 6);
        return function (x) {
            var v_m = (R * Math.pow(x, 3) / 6 - load * Math.pow(x, 4) / 24 + C1 * x) / EI;
            return {
                x: x,
                y: v_m * 1000 // Convert m to mm for display
            };
        };
    }
    getBendingMomentEquation(beam, load) {
        var L = beam.primarySpan;
        var R = (load * L) / 2;
        return function (x) {
            return {
                x: x,
                y: R * x - (load * Math.pow(x, 2) / 2)
            };
        };
    }
    getShearForceEquation(beam, load) {
        var L = beam.primarySpan;
        var R = (load * L) / 2;
        return function (x) {
            return {
                x: x,
                y: R - load * x
            };
        };
    }
};


/**
 * Calculate deflection, bending stress and shear stress for a beam with two spans of equal condition
 *
 * @param {Beam}   beam   The beam object
 * @param {Number}  load    The applied load
 */
BeamAnalysis.analyzer.twoSpanUnequal = class {
    constructor(beam, load) {
        this.beam = beam;
        this.load = load;
    }
    _getConstants(beam, load) {
        var L1 = beam.primarySpan;
        var L2 = beam.secondarySpan;
        var w = load;
        var M2 = -w * (Math.pow(L1, 3) + Math.pow(L2, 3)) / (8 * (L1 + L2));
        var R1 = (w * L1 / 2) + (M2 / L1);
        var R2L = (w * L1 / 2) - (M2 / L1);
        var R2R = (w * L2 / 2) - (M2 / L2);
        var R2 = R2L + R2R;
        var C1 = (w * Math.pow(L1, 3) / 24) - (R1 * Math.pow(L1, 2) / 6);
        var D1 = (w * Math.pow(L2, 3) / 24) - (M2 * L2 / 2) - (R2R * Math.pow(L2, 2) / 6);
        var EI = beam.material.properties.EI / 1000000000;
        
        return { L1, L2, w, M2, R1, R2L, R2R, R2, C1, D1, EI };
    }
    getDeflectionEquation(beam, load) {
        var c = this._getConstants(beam, load);
        return function (x) {
            var v_m = 0;
            if (x <= c.L1) {
                v_m = (c.R1 * Math.pow(x, 3) / 6 - c.w * Math.pow(x, 4) / 24 + c.C1 * x) / c.EI;
            } else {
                var x2 = x - c.L1;
                v_m = (c.M2 * Math.pow(x2, 2) / 2 + c.R2R * Math.pow(x2, 3) / 6 - c.w * Math.pow(x2, 4) / 24 + c.D1 * x2) / c.EI;
            }
            return { x: x, y: v_m * 1000 };
        };
    }
    getBendingMomentEquation(beam, load) {
        var c = this._getConstants(beam, load);
        return function (x) {
            var m = 0;
            if (x <= c.L1) {
                m = c.R1 * x - (c.w * Math.pow(x, 2) / 2);
            } else {
                var x2 = x - c.L1;
                m = c.M2 + c.R2R * x2 - (c.w * Math.pow(x2, 2) / 2);
            }
            return { x: x, y: m };
        };
    }
    getShearForceEquation(beam, load) {
        var c = this._getConstants(beam, load);
        return function (x) {
            var v = 0;
            if (x <= c.L1) {
                v = c.R1 - c.w * x;
            } else {
                var x2 = x - c.L1;
                v = c.R2R - c.w * x2;
            }
            return { x: x, y: v };
        };
    }
};
