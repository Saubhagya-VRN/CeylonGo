<?php
/**
 * Guide Request API
 * Handles API requests for tour guide requests
 */

class GuideRequestAPI {
    private $db;
    private $model;

    public function __construct() {
        $this->db = Database::getConnection();
        require_once BASE_PATH . '/models/GuideRequest.php';
        $this->model = new GuideRequest($this->db);
    }

    public function handleRequest($method, $segments) {
        switch ($method) {
            case 'GET':
                if (isset($segments[1]) && is_numeric($segments[1])) {
                    $this->getById($segments[1]);
                } else {
                    $this->getAll();
                }
                break;
                
            case 'POST':
                $this->create();
                break;
                
            case 'PUT':
                if (isset($segments[1]) && is_numeric($segments[1])) {
                    $this->update($segments[1]);
                }
                break;
                
            case 'DELETE':
                if (isset($segments[1]) && is_numeric($segments[1])) {
                    $this->delete($segments[1]);
                }
                break;
                
            default:
                http_response_code(405);
                echo json_encode(['error' => 'Method not allowed']);
                break;
        }
    }

    private function getAll() {
        try {
            $requests = $this->model->getAll();
            echo json_encode(['success' => true, 'data' => $requests]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    private function getById($id) {
        try {
            $request = $this->model->getById($id);
            if ($request) {
                echo json_encode(['success' => true, 'data' => $request]);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Request not found']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    private function create() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            // Validate required fields
            $required = ['customerName', 'contactNumber', 'location', 'language', 'date', 'time'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'error' => "Missing required field: $field"]);
                    return;
                }
            }

            $this->model->customerName = $data['customerName'];
            $this->model->contactNumber = $data['contactNumber'];
            $this->model->location = $data['location'];
            $this->model->language = $data['language'];
            $this->model->date = $data['date'];
            $this->model->time = $data['time'];
            $this->model->notes = $data['notes'] ?? '';

            if ($this->model->create()) {
                http_response_code(201);
                echo json_encode(['success' => true, 'data' => ['id' => $this->model->id]]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => 'Failed to create request']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    private function delete($id) {
        try {
            if ($this->model->delete($id)) {
                echo json_encode(['success' => true, 'message' => 'Request deleted successfully']);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => 'Failed to delete request']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}
