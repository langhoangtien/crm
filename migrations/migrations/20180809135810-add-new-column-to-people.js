'use strict';

var dbm;
var type;
var seed;

/**
 * We receive the dbmigrate dependency from dbmigrate initially.
 * This enables us to not have to rely on NODE_PATH.
 */
exports.setup = function(options, seedLink) {
    dbm = options.dbmigrate;
    type = dbm.dataType;
    seed = seedLink;
};

exports.up = function(db, callback) {
    db.addColumn('phppos_people', 'first_date_registration', {type: 'date'}, callback);
    db.addColumn('phppos_people', 'last_updated_registration', {type: 'date'}, callback);
};

exports.down = function(db, callback) {
    db.removeColumn('phppos_people', 'first_date_registration', callback);
    db.removeColumn('phppos_people', 'last_updated_registration', callback);
};

exports._meta = {
    "version": 1
};
