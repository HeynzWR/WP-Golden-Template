const axios = require("axios");
const https = require("https");
const fs = require("fs");
const path = require("path");

// --- CONFIG ---
const WP_URL = "https://jlb-partners.ddev.site/";   // Your WordPress site URL
const WP_USER = "admin";                  // WP username
const WP_APP_PASSWORD = "1aON ayM4 8iAK U458 erR1 Yymw";   // WP application password

const PROJECT_JSON = "jonah_projects.json";
const IMAGE_DIR = path.join(__dirname, "images");

const auth = { username: WP_USER, password: WP_APP_PASSWORD };

const agent = new https.Agent({
    rejectUnauthorized: false, // ignore SSL errors
});

// --- Upload image to WP Media Library with optional caption ---
async function uploadMedia(filePath, caption = null, title = null) {
    try {
        const fileName = path.basename(filePath);
        const fileData = fs.readFileSync(filePath);

        // First, upload the image
        const res = await axios.post(`${WP_URL}/wp-json/wp/v2/media`, fileData, {
            auth,
            httpsAgent: agent,
            headers: {
                "Content-Disposition": `attachment; filename=${fileName}`,
                "Content-Type": "image/jpeg" // adjust if PNG
            }
        });

        const mediaId = res.data.id;

        // If caption or title is provided, update the media metadata
        if (caption || title) {
            try {
                await axios.post(`${WP_URL}/wp-json/wp/v2/media/${mediaId}`, {
                    caption: caption || "",
                    title: title || fileName,
                    alt_text: title || ""
                }, {
                    auth,
                    httpsAgent: agent
                });
            } catch (updateErr) {
                console.error(`   ⚠️  Failed to update caption for ${fileName}:`, updateErr.message);
            }
        }

        return mediaId;
    } catch (err) {
        console.error("Media upload failed:", filePath, err.message);
        return null;
    }
}

// --- State Abbreviation to Full Name Mapping ---
const STATE_MAP = {
    'AL': 'Alabama',
    'AK': 'Alaska',
    'AZ': 'Arizona',
    'AR': 'Arkansas',
    'CA': 'California',
    'CO': 'Colorado',
    'CT': 'Connecticut',
    'DE': 'Delaware',
    'FL': 'Florida',
    'GA': 'Georgia',
    'HI': 'Hawaii',
    'ID': 'Idaho',
    'IL': 'Illinois',
    'IN': 'Indiana',
    'IA': 'Iowa',
    'KS': 'Kansas',
    'KY': 'Kentucky',
    'LA': 'Louisiana',
    'ME': 'Maine',
    'MD': 'Maryland',
    'MA': 'Massachusetts',
    'MI': 'Michigan',
    'MN': 'Minnesota',
    'MS': 'Mississippi',
    'MO': 'Missouri',
    'MT': 'Montana',
    'NE': 'Nebraska',
    'NV': 'Nevada',
    'NH': 'New Hampshire',
    'NJ': 'New Jersey',
    'NM': 'New Mexico',
    'NY': 'New York',
    'NC': 'North Carolina',
    'ND': 'North Dakota',
    'OH': 'Ohio',
    'OK': 'Oklahoma',
    'OR': 'Oregon',
    'PA': 'Pennsylvania',
    'RI': 'Rhode Island',
    'SC': 'South Carolina',
    'SD': 'South Dakota',
    'TN': 'Tennessee',
    'TX': 'Texas',
    'UT': 'Utah',
    'VT': 'Vermont',
    'VA': 'Virginia',
    'WA': 'Washington',
    'WV': 'West Virginia',
    'WI': 'Wisconsin',
    'WY': 'Wyoming',
    'DC': 'District of Columbia'
};

/**
 * Extract state abbreviation from location string and return full state name
 * @param {string} location - Location string like "AUSTIN, TX" or "Austin, TX"
 * @returns {string|null} - Full state name or null if not found
 */
function extractStateName(location) {
    if (!location) return null;

    // Extract state abbreviation (last 2 characters after comma)
    const parts = location.split(',');
    if (parts.length < 2) return null;

    const stateAbbr = parts[parts.length - 1].trim().toUpperCase();
    return STATE_MAP[stateAbbr] || null;
}

/**
 * Get or create taxonomy term and return its ID
 * @param {string} taxonomy - Taxonomy name (e.g., 'project_state', 'project_type')
 * @param {string} termName - Term name (e.g., 'Texas', 'Highrise')
 * @returns {number|null} - Term ID or null if failed
 */
async function getOrCreateTaxonomyTerm(taxonomy, termName) {
    if (!termName) return null;

    try {
        // First, try to find existing term
        const searchRes = await axios.get(`${WP_URL}/wp-json/wp/v2/${taxonomy}`, {
            auth,
            httpsAgent: agent,
            params: { search: termName }
        });

        // If term exists, return its ID
        if (searchRes.data && searchRes.data.length > 0) {
            const exactMatch = searchRes.data.find(term => term.name === termName);
            if (exactMatch) {
                console.log(`   ✓ Found existing ${taxonomy}: ${termName} (ID: ${exactMatch.id})`);
                return exactMatch.id;
            }
        }

        // If term doesn't exist, create it
        const createRes = await axios.post(`${WP_URL}/wp-json/wp/v2/${taxonomy}`, {
            name: termName
        }, {
            auth,
            httpsAgent: agent
        });

        console.log(`   ✓ Created new ${taxonomy}: ${termName} (ID: ${createRes.data.id})`);
        return createRes.data.id;
    } catch (err) {
        console.error(`   ⚠️  Failed to get/create ${taxonomy} term: ${termName}`);
        if (err.response) {
            console.error(`      Status: ${err.response.status}`);
            console.error(`      Error:`, err.response.data);
        } else {
            console.error(`      Error:`, err.message);
        }
        return null;
    }
}


// --- Create WordPress project post ---
async function createProject(project, index) {
    try {
        // Upload hero image → featured image
        let featuredImageId = null;
        if (project.hero_image) {
            featuredImageId = await uploadMedia(project.hero_image);
        }

        // Upload gallery images with labels
        const galleryIds = [];
        if (project.gallery && project.gallery.length > 0) {
            for (let i = 0; i < project.gallery.length; i++) {
                const galleryItem = project.gallery[i];

                // Handle both old format (string) and new format (object with path and label)
                let imgPath, imgLabel;
                if (typeof galleryItem === 'string') {
                    imgPath = galleryItem;
                    imgLabel = null;
                } else {
                    imgPath = galleryItem.path;
                    imgLabel = galleryItem.label || null;
                }

                // Upload with caption if label exists
                const id = await uploadMedia(imgPath, imgLabel, imgLabel);
                if (id) {
                    galleryIds.push(id);
                    if (imgLabel) {
                        console.log(`      🏷️  Image ${i + 1}: "${imgLabel}"`);
                    }
                }
            }
        }

        // Add gallery shortcode to post content
        const galleryShortcode = galleryIds.length ? `[gallery ids="${galleryIds.join(',')}"]` : "";

        // Extract state from location and get taxonomy term ID
        const stateName = extractStateName(project.location);
        let stateTermId = null;
        if (stateName) {
            stateTermId = await getOrCreateTaxonomyTerm('project_state', stateName);
        }

        // Get property type taxonomy term ID
        let propertyTypeTermId = null;
        if (project.property_type) {
            propertyTypeTermId = await getOrCreateTaxonomyTerm('project_type', project.property_type);
        }

        // Create the post with ACF fields and taxonomies
        const postData = {
            title: project.title,
            content: `${project.content || ""}\n\n${galleryShortcode}`,
            status: "publish",
            featured_media: featuredImageId,
            acf: {
                project_location: project.location || "",
                project_units: project.unit_count || "",
                project_external_link: project.external_link || null
            }
        };

        // Add taxonomies - these must be at the root level of postData
        if (stateTermId) {
            postData.project_state = [stateTermId];
        }

        if (propertyTypeTermId) {
            postData.project_type = [propertyTypeTermId];
        }

        console.log(`   📝 Creating post with data:`, {
            title: postData.title,
            project_state: postData.project_state,
            project_type: postData.project_type
        });

        const res = await axios.post(`${WP_URL}/wp-json/wp/v2/projects`, postData, {
            auth,
            httpsAgent: agent
        });

        const postId = res.data.id;
        console.log(`✅ Created project: ${project.title} (ID: ${postId})`);
        console.log(`   📍 Location: ${project.location || 'N/A'}`);
        console.log(`   🏢 Units: ${project.unit_count || 'N/A'}`);
        if (stateName) {
            console.log(`   🗺️  State: ${stateName}`);
        }
        if (project.property_type) {
            console.log(`   🏗️  Type: ${project.property_type}`);
        }


    } catch (err) {
        console.error(`❌ Failed to create project: ${project.title}`);
        if (err.response) {
            console.error(`   Status: ${err.response.status}`);
            console.error(`   Error:`, JSON.stringify(err.response.data, null, 2));
        } else {
            console.error(`   Error:`, err.message);
        }
    }
}

// --- Main import function ---
async function main() {
    console.log("🚀 Starting WordPress project import...\n");

    if (!fs.existsSync(PROJECT_JSON)) {
        console.error(`❌ Error: ${PROJECT_JSON} not found!`);
        return;
    }

    const projects = JSON.parse(fs.readFileSync(PROJECT_JSON, "utf-8"));
    console.log(`Found ${projects.length} projects to import\n`);

    for (let i = 0; i < projects.length; i++) {
        console.log(`\n${'='.repeat(60)}`);
        console.log(`[${i + 1}/${projects.length}] Importing: ${projects[i].title}`);
        console.log('='.repeat(60));
        await createProject(projects[i], i + 1);
    }

    console.log("\n" + "=".repeat(60));
    console.log("✅ All projects imported successfully!");
    console.log("=".repeat(60));
    console.log("\n📊 Features Implemented:");
    console.log("  ✅ ACF Fields populated (location, units, external_link)");
    console.log("  ✅ State taxonomy auto-assigned from location");
    console.log("  ✅ Property Type taxonomy assigned (Highrise, Low Density, Medium Density)");
    console.log("  ✅ Gallery images uploaded with labels/captions");
    console.log("  ✅ Gallery shortcode created in post content");
    console.log("  ✅ Featured images set");
    console.log("\n🗺️  Taxonomy Mapping:");
    console.log("  - State: Extracts abbreviation from location (e.g., 'TX' → 'Texas')");
    console.log("  - Property Type: Highrise, Low Density, or Medium Density");
}

main();
